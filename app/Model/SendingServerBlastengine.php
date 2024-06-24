<?php

/**
 * SendingServerSendGrid class.
 *
 * Abstract class for SendGrid sending servers
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Acelle\Library\StringHelper;
use Acelle\Library\SendingServer\DomainVerificationInterface;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Exception;

class SendingServerBlastengine extends SendingServer implements DomainVerificationInterface
{
    protected $table = 'sending_servers';
    protected $logger;

    public const WEBHOOK = 'blastengine';
    public const EVENT_TYPE_HARDERROR = 'HARDERROR';
    public const EVENT_TYPE_SOFTERROR = 'SOFTERROR';
    public const EVENT_TYPE_DROP = 'DROP';

    /**
     * Setup webhooks for processing bounce and feedback loop.
     *
     * @return mixed
     */
    public function setupWebhooks()
    {
    }

    /**
     * Get verified identities (domains and email addresses).
     *
     * @return bool
     */
    public function syncIdentities()
    {
    }

    /**
     * Check the sending server settings, make sure it does work.
     *
     * @return bool
     */
    public function test()
    {
        return true;
    }

    /**
     * Allow user to verify his/her own sending domain against Acelle Mail.
     *
     * @return bool
     */
    public function allowVerifyingOwnDomains()
    {
        return true;
    }

    public function allowOtherSendingDomains()
    {
        return true;
    }

    /**
     * Allow user to verify his/her own sending domain against Acelle Mail.
     *
     * @return bool
     */
    public function allowVerifyingOwnEmails()
    {
        return false;
    }

    /**
     * Allow user to verify his/her own emails against AWS.
     *
     * @return bool
     */
    public function allowVerifyingOwnDomainsRemotely()
    {
        return false;
    }

    /**
     * Allow user to verify his/her own emails against AWS.
     *
     * @return bool
     */
    public function allowVerifyingOwnEmailsRemotely()
    {
        return false;
    }

    public function setupBeforeSend($fromEmailAddress)
    {
    }

    public function checkDomainVerificationStatus($domain): array
    {
    }

    public function verifyDomain($domain): array
    {
        // Blastengine does not actually verify any domain
    }

    public static function handleNotification()
    {
        $logger = self::initLogger();

        try {
            /*
             * Sample raw:
             *     {"events":[{"event":{"type":"HARDERROR","datetime":"2023-01-19T12:26:10+09:00","detail":{"mailaddress":"louisit343243243234kjsasasdfvn@gmail.com","subject":"Test","error_code":"550","error_message":"宛先のメールアドレスがありません","delivery_id":2}}}]}
             *
             *
             */

            $raw = file_get_contents('php://input');

            // $raw = '{"events":[{"event":{"type":"HARDERROR","datetime":"2023-01-19T12:26:10+09:00","detail":{"mailaddress":"louisit343243243234kjsasasdfvn@gmail.com","subject":"Test","error_code":"550","error_message":"宛先のメールアドレスがありません","delivery_id":2}}}]}';

            $logger->info("Received: \n{$raw}");
            $json = json_decode($raw, true);

            if (is_null($json)) {
                throw new Exception('php://input is EMPTY');
            }

            foreach ($json['events'] as $event) {
                $type = $event['event']['type'];
                $deliveryId = $event['event']['detail']['delivery_id'];

                $logger->info('Great, got Event Type and Delivery ID');
                $logger->info("=> TYPE: {$type}, DELIVERY ID: {$deliveryId}");

                if (in_array($type, [self::EVENT_TYPE_HARDERROR, self::EVENT_TYPE_DROP])) {
                    BounceLog::recordHardBounce($deliveryId, $raw, function ($message) use ($logger) {
                        $logger->info($message);
                    });
                } else {
                    $logger->info("Thank God, it is not a HARDERROR or DROP event! It is just: {$type}");
                }
            }
        } catch (\Throwable $ex) {
            $logger->warning($ex->getMessage());
        }
    }

    public static function initLogger()
    {
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n");

        $logfile = storage_path("logs/handler-blastengine.log");
        $stream = new RotatingFileHandler($logfile, 0, Logger::DEBUG);
        $stream->setFormatter($formatter);

        $pid = getmypid();
        $logger = new Logger($pid);
        $logger->pushHandler($stream);

        return $logger;
    }
}
