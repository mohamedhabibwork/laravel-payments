<?php

namespace Habib\LaravelPayments\Gateways;

use Habib\LaravelPayments\Configs\ConfigInterface;
use Habib\LaravelPayments\Users\PaymobUser;
use Habib\LaravelPayments\Users\UserInterface;
use Illuminate\Http\Client\PendingRequest;

abstract class Gateway implements GatewayInterface
{
    protected PendingRequest $http;

    protected UserInterface $user;

    protected ConfigInterface $config;

    public function setHttp(PendingRequest $http): self
    {
        $this->http = $http;

        return $this;
    }

    public function setConfig(ConfigInterface $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function setUser(PaymobUser|UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function generateCode(string $name = 'order'): string
    {
        return implode('-', [$name, time()]);
    }
}
