<?php

use Acelle\Library\RateTracker;
use Acelle\Library\CreditTracker;
use Acelle\Library\Exception\OutOfCredits;

use function Acelle\Helpers\execute_with_limits;

test('CreditLimit just works', function () {
    $creditFile1 = '/tmp/test-rate-'.uniqid();
    $creditTracker1 = CreditTracker::load($creditFile1, $createFile = true);
    $creditTracker1->setCredits(5);

    $creditFile2 = '/tmp/test-rate-'.uniqid();
    $creditTracker2 = CreditTracker::load($creditFile2, $createFile = true);
    $creditTracker2->setCredits(2);

    // Execute 1st time -> ok
    $task = fn () => execute_with_limits([  ], [ $creditTracker1, $creditTracker2 ]);

    // First execution OK
    $task();
    expect($creditTracker1->getRemainingCredits())->toBe(4);
    expect($creditTracker2->getRemainingCredits())->toBe(1);

    // Second execution OK
    $task();
    expect($creditTracker1->getRemainingCredits())->toBe(3);
    expect($creditTracker2->getRemainingCredits())->toBe(0);

    // Second execution results in exception
    expect($task)->toThrow(OutOfCredits::class);

    // Rollback tracker 1
    expect($creditTracker1->getRemainingCredits())->toBe(3);

    // Tracker 2 still 0
    expect($creditTracker2->getRemainingCredits())->toBe(0);
});

test('CreditLimit just works with task exception', function () {
    $creditFile1 = '/tmp/test-rate-'.uniqid();
    $creditTracker1 = CreditTracker::load($creditFile1, $createFile = true);
    $creditTracker1->setCredits(100);

    $creditFile2 = '/tmp/test-rate-'.uniqid();
    $creditTracker2 = CreditTracker::load($creditFile2, $createFile = true);
    $creditTracker2->setCredits(100);

    // Task
    $task = fn () => execute_with_limits([  ], [ $creditTracker1, $creditTracker2 ], function () {
        throw new \Exception();
    });

    // Second execution results in exception
    expect($task)->toThrow(\Exception::class);

    // Rollback tracker 1
    expect($creditTracker1->getRemainingCredits())->toBe(100);

    // Rollback tracker 2
    expect($creditTracker2->getRemainingCredits())->toBe(100);
});
