<?php

namespace Habib\LaravelPayments\Facades;

use Habib\LaravelPayments\Gateways\TapGateway;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin TapGateway
 */
class TapFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TapGateway::class;
    }
}
