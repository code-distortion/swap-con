<?php

namespace CodeDistortion\SwapCon\Tests\Laravel;

use CodeDistortion\SwapCon\SwapConFacade;
use CodeDistortion\SwapCon\SwapConServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Jchook\AssertThrows\AssertThrows;

/**
 * The test case that unit tests extend from
 */
class TestCase extends BaseTestCase
{
    use AssertThrows;

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app The Laravel app.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SwapConServiceProvider::class
        ];
    }

    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app The Laravel app.
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'SwapCon' => SwapConFacade::class
        ];
    }
}
