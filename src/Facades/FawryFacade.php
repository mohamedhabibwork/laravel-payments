<?php

namespace Habib\LaravelPayments\Facades;

use Habib\LaravelPayments\Gateways\FawryGateway;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin FawryGateway
 */
class FawryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FawryGateway::class;
    }
}
