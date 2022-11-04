<?php

namespace LaravelARay\LaravelARay;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LaravelARay\LaravelARay\Skeleton\SkeletonClass
 */
class LaravelARayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel a-ray';
    }
}
