<?php

namespace Habib\LaravelPayments\Gateways;

use Habib\LaravelPayments\Configs\ConfigInterface;
use Habib\LaravelPayments\Users\UserInterface;
use Illuminate\Http\Client\PendingRequest;

interface GatewayInterface
{
    public function setHttp(PendingRequest $http): self;

    public function setConfig(ConfigInterface $config): self;

    public function setUser(UserInterface $user): self;
}
