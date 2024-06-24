<?php

use Acelle\Library\Lockable;

// To test concurrency
// Step 1: Make sure sleep(x) with "x" is higher than value "v" of $waitTimeout(v)
// Step 2: Execute two tests in two console c1 (first) and c2 (later): ./vendor/bin/pest tests/Unit/LockTest.php
// Expected result: c1 shall take x seconds to finish
//                  c2 shall take v seconds to time out (with the "No exception" message;
test('Exclusive lock just works', function () {
    $file = '/tmp/gaugau';
    $task = function () {
        sleep(1);
        // Just do nothing here
    };
    $waitTimeout = 2;
    $waitTimeoutCallback = function () {
        // Do not throw an excpetion if a waitTimeoutCallback is present
        echo "No exception\n";
    };

    Lockable::withExclusiveLock($file, $task, $waitTimeout, $waitTimeoutCallback);

    expect(true)->toBe(true);
});
