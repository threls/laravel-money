<?php

namespace Threls\LaravelMoney\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Threls\LaravelMoney\LaravelMoney
 */
class LaravelMoney extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravelmoney';
    }
}
