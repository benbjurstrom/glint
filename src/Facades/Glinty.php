<?php

namespace BenBjurstrom\Glinty\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BenBjurstrom\Glinty\Glinty
 */
class Glinty extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BenBjurstrom\Glinty\Glinty::class;
    }
}
