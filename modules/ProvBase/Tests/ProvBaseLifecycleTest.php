<?php

namespace Modules\ProvBase\Tests;

/**
 * Run the lifecycle test for ProvBase.
 */
class ProvBaseLifecycleTest extends \BaseLifecycleTest
{
    // no MVC – nothing to test
    protected $tests_to_be_excluded = [
        'testCreateTwiceUsingTheSameData',
        'testCreateWithFakeData',
        'testDatatableDataReturned',
        'testDeleteFromIndexView',
        'testEmptyCreate',
        'testIndexViewVisible',
        'testUpdate',
    ];
}
