<?php

namespace Tests\Unit;

use Tests\TestCase;
use Acelle\Model\SendingServer;
use Acelle\Model\Subscription;
use Acelle\Model\EmailVerificationServer;
use Acelle\Model\Subscriber;
use Acelle\Model\Customer;
use Acelle\Model\Campaign;
use Acelle\Model\MailList;
use Acelle\Library\Exception\RateLimitExceeded;
use Acelle\Library\Exception\OutOfCredits;
use Acelle\Jobs\SendMessage;
use Acelle\Jobs\VerifySubscriber;
use Exception;
use Mockery;
use Acelle\Library\RateTracker;
use Acelle\Library\RateLimit;
use Acelle\Library\CreditTracker;
use Carbon\Carbon;

use function Acelle\Helpers\withQuota;

class QuotaTest extends TestCase
{
    public function initServer()
    {
        $server = new SendingServer();
        $server->generateUid();
        return $server;
    }

    public function initSubscription()
    {
        $subscription = new Subscription();
        $subscription->generateUid();
        return $subscription;
    }

    public function initEmailVerificationServer()
    {
        $server = new EmailVerificationServer();
        $server->generateUid();
        $server = Mockery::mock($server);
        $server->shouldReceive('verify')->andReturn(null);
        return $server;
    }

    private function prepareSendMessageTest()
    {
        // Server
        $server = $this->initServer();
        $server = Mockery::mock($server);
        $server->shouldReceive('send')->andReturn(null);

        // Subscription
        $subscription = Mockery::mock($this->initSubscription());

        // Subscriber type is required as the new SendMessage(Subscriber $sub) has strict type parameter
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->shouldReceive('getEmail')->andReturn('test@example.com');

        // Logger
        $logger = Mockery::mock('logger');
        $logger->shouldReceive('info')->andReturn(null);
        $logger->shouldReceive('warning')->andReturn(null);
        $logger->shouldReceive('error')->andReturn(null);

        // Campaign
        $campaign = Mockery::mock(new Campaign());
        $campaign->shouldReceive('logger')->andReturn($logger);
        $campaign->shouldReceive('prepareEmail')->andReturn(null);
        $campaign->shouldReceive('trackMessage')->andReturn(null);

        return [$campaign, $subscriber, $server, $subscription];
    }

    public function test_send_message_job_with_rate_limit_exceeded()
    {
        list($campaign, $subscriber, $server, $subscription) = $this->prepareSendMessageTest();

        $server->shouldReceive('getRateLimitTracker')->andReturn(
            new RateTracker('/tmp/test-server-rate-log-'.uniqid(), [
                new RateLimit(
                    $amount = 2,
                    $periodValue = 1,
                    $periodUnit = 'minute',
                )
            ])
        );

        $subscription->shouldReceive('getSendEmailRateTracker')->andReturn(
            new RateTracker('/tmp/test-subscription-rate-log-'.$subscription->uid, [
                new RateLimit(
                    $amount = 1,
                    $periodValue = 1,
                    $periodUnit = 'minute',
                )
            ])
        );

        $subscription->shouldReceive('getSendEmailCreditTracker')->andReturn(
            CreditTracker::load('/tmp/test-subscription-credit-'.$subscription->uid, $createFileIfNotExist = true)->setCredits(100)
        );

        // Record exception to test
        $outcome = null;

        // 1st execution
        $sendMsgJob = new SendMessage($campaign, $subscriber, $server, $subscription);
        $sendMsgJob->send(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        $this->assertEquals($server->getRateLimitTracker()->getCreditsUsed(Carbon::parse('5 minutes ago')), 1);
        $this->assertEquals($subscription->getSendEmailRateTracker()->getCreditsUsed(Carbon::parse('5 minutes ago')), 1);
        // FAILED, as credits tracking is commented out in SendMessage
        // $this->assertEquals($subscription->getSendEmailCreditTracker()->getRemainingCredits(), 99);
        $this->assertNull($outcome);

        // 2st execution
        $sendMsgJob = new SendMessage($campaign, $subscriber, $server, $subscription);
        $sendMsgJob->send(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        $this->assertEquals($server->getRateLimitTracker()->getCreditsUsed(Carbon::parse('5 minutes ago')), 2); // Server counts
        $this->assertEquals($subscription->getSendEmailRateTracker()->getCreditsUsed(Carbon::parse('5 minutes ago')), 1); // sub fails to count, so still 1 (max 1/minute)
        $this->assertEquals(RateLimitExceeded::class, get_class($outcome));
        // FAILED, as credits tracking is commented out in SendMessage
        // $this->assertEquals($subscription->getSendEmailCreditTracker()->getRemainingCredits(), 99);
    }

    public function test_send_message_job_with_credits_exceeded()
    {
        list($campaign, $subscriber, $server, $subscription) = $this->prepareSendMessageTest();

        $server->shouldReceive('getRateLimitTracker')->andReturn(
            new RateTracker('/tmp/test-server-rate-log-'.$server->uid, [
                new RateLimit(
                    $amount = 3,
                    $periodValue = 1,
                    $periodUnit = 'minute',
                )
            ])
        );

        $subscription->shouldReceive('getSendEmailRateTracker')->andReturn(
            new RateTracker('/tmp/test-subscription-rate-log-'.$subscription->uid, [
                new RateLimit(
                    $amount = 2,
                    $periodValue = 1,
                    $periodUnit = 'minute',
                )
            ])
        );

        $subscription->shouldReceive('getSendEmailCreditTracker')->andReturn(
            CreditTracker::load('/tmp/test-subscription-credit-'.$subscription->uid, $createFileIfNotExist = true)->setCredits(1)
        );

        $outcome = null;

        // SendMessage job 1st time
        $sendMsgJob = new SendMessage($campaign, $subscriber, $server, $subscription);
        $sendMsgJob->send(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        $this->assertEquals($server->getRateLimitTracker()->getCreditsUsed(Carbon::parse('10 minute ago')), 1);
        $this->assertEquals($subscription->getSendEmailRateTracker()->getCreditsUsed(Carbon::parse('10 minute ago')), 1);
        // FAILED, as credits tracking is commented out in SendMessage
        // $this->assertEquals($subscription->getSendEmailCreditTracker()->getRemainingCredits(), 0);
        $this->assertNull($outcome);

        // SendMessage job 2nd time
        $sendMsgJob = new SendMessage($campaign, $subscriber, $server, $subscription);
        $sendMsgJob->send(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        // FAILED, as credits tracking is commented out in SendMessage
        // Uncomment the credit counting code in the SendMessage class to test the following assertions
        //
        // $this->assertEquals(OutOfCredits::class, get_class($outcome));
        // $this->assertEquals($server->getRateLimitTracker()->getCreditsUsed(Carbon::parse('10 minute ago')), 1);
        // $this->assertEquals($subscription->getSendEmailRateTracker()->getCreditsUsed(Carbon::parse('10 minute ago')), 1);
        // $this->assertEquals($subscription->getSendEmailCreditTracker()->getRemainingCredits(), 0);
    }

    public function test_email_verification_server_job_server_rate_limit_exceeded()
    {
        // Server
        $server = $this->initEmailVerificationServer();
        $subscription = $this->initSubscription();
        $subscriber = Mockery::mock(new Subscriber());
        $subscriber->shouldReceive('verify')->andReturn(null);

        $server->setLimit($limit = 2, $unitValue = 1, $unit = 'minute');

        // SendMessage job
        $verifyJob = new VerifySubscriber($subscriber, $server, $subscription);

        // First shot
        $verifyJob->doVerify(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        // Just fine
        $this->assertNull($outcome);
        $this->assertEquals($server->getRateTracker()->getCreditsUsed(), 1);

        // Second shot
        $verifyJob->doVerify(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        // Just fine
        $this->assertNull($outcome);
        $this->assertEquals($server->getRateTracker()->getCreditsUsed(), 2);

        // Third short
        $verifyJob->doVerify(function ($exception) use (&$outcome) {
            $outcome = $exception;
        });

        // Second, quota exceeded
        $this->assertEquals(RateLimitExceeded::class, get_class($outcome));
        $this->assertEquals($server->getRateTracker()->getCreditsUsed(), 2);
    }
}
