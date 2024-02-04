<?php

namespace Habib\LaravelPayments\Facades;

use Habib\LaravelPayments\Gateways\PaymobGateway;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin PaymobGateway
 */
class PaymobFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaymobGateway::class;
    }
}
