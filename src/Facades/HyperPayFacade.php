<?php

namespace Habib\LaravelPayments\Facades;

use Habib\LaravelPayments\Gateways\HyperPayGateway;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin  HyperPayGateway
 */
class HyperPayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HyperPayGateway::class;
    }
}
