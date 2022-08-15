<?php

namespace Lyhty\Macronite\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Lyhty\Macronite\MacroniteServiceProvider::class,
            TestMacroServiceProvider::class,
        ];
    }
}
