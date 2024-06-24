<?php

use Acelle\Library\CreditTracker;
use Acelle\Library\Exception\OutOfCredits;

test('Credit tracker just works', function () {
    $file = '/tmp/test-rate-'.uniqid();
    $tracker = CreditTracker::load($file, $createFile = true);

    expect($tracker->getRemainingCredits())->toBe(CreditTracker::UNLIMITED);

    $tracker->setCredits(2);
    expect($tracker->getRemainingCredits())->toBe(2);

    // Count to deduct credit
    $tracker->count();
    expect($tracker->getRemainingCredits())->toBe(1);

    // Count to deduct credit
    $tracker->count();
    expect($tracker->getRemainingCredits())->toBe(0);
});

test('Credit tracker should throw OutOfCredits', function () {
    $file = '/tmp/test-rate-'.uniqid();
    $tracker = CreditTracker::load($file, $createFile = true);
    $tracker->setCredits(1);   // credits = 1
    $tracker->count();         // credits = 0

    // Since credit is already ZERO, another count() shall throw an exception
    $tracker->count();
})->throws(OutOfCredits::class);


test('Cannot assign invalid values for CreditTracker (non-int)', function () {
    $file = '/tmp/test-rate-'.uniqid();
    $tracker = CreditTracker::load($file, $createFile = true);
    $tracker->setCredits('xxxxx');   // invalid
})->throws(Exception::class);

test('Cannot assign invalid values for CreditTracker (<-1)', function () {
    $file = '/tmp/test-rate-'.uniqid();
    $tracker = CreditTracker::load($file, $createFile = true);
    $tracker->setCredits(-2);   // invalid
})->throws(Exception::class);
