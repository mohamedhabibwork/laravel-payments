<?php

namespace Habib\LaravelPayments\Configs;

use Habib\LaravelPayments\Gateways\GatewayInterface;
use Illuminate\Config\Repository;

interface ConfigInterface
{
    public static function initConfig(Repository $config): self;

    public function getGateway(): GatewayInterface;

    public function getMode(): bool;
}
