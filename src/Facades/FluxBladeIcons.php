<?php

namespace Elemind\FluxBladeIcons\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elemind\FluxBladeIcons\FluxBladeIcons
 */
class FluxBladeIcons extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Elemind\FluxBladeIcons\FluxBladeIcons::class;
    }
}
