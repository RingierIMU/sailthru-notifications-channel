<?php

use NotificationChannels\Sailthru\Test\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure passed to pest()->extend() binds the given TestCase to all
| tests in the specified directory. Every test file in tests/ will use
| the Sailthru TestCase which boots a Laravel application via Testbench.
|
*/

pest()->extend(TestCase::class)->in(__DIR__);
