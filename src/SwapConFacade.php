<?php

namespace CodeDistortion\SwapCon;

use Illuminate\Support\Facades\Facade;

/**
 * SwapConFacade Facade for Laravel.
 */
class SwapConFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'SwapCon';
    }
}
