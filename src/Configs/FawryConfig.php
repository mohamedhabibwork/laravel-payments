<?php

namespace Habib\LaravelPayments\Configs;

use Habib\LaravelPayments\Gateways\FawryGateway;
use Habib\LaravelPayments\Gateways\GatewayInterface;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class FawryConfig implements Arrayable, ConfigInterface, Jsonable
{
    public string $url = 'https://atfawry.fawrystaging.com/';

    public function __construct(
        public string $merchant,
        public string $secret,
        public string $returnUrl = '',
        public string $display_mode = 'POPUP', //  [POPUP, INSIDE_PAGE, SIDE_PAGE , SEPARATED]
        public string $pay_mode = 'CARD', // ['CashOnDelivery', 'PayAtFawry', 'MWALLET', 'CARD' , 'VALU']
        public int $expiry = 72, // hours
        public bool $is_live = false,
        ?string $url = null,
    ) {
        if ($url) {
            $this->url = $url;
        } elseif (is_null($url) && $this->is_live) {
            $this->url = 'https://www.atfawry.com/';
        }
    }

    public static function initConfig(Repository $config): ConfigInterface
    {
        return new self(
            merchant: $config->get('payments.fawry.merchant'),
            secret: $config->get('payments.fawry.secret'),
            returnUrl: $config->get('payments.fawry.returnUrl'),
            display_mode: $config->get('payments.fawry.display_mode'),
            pay_mode: $config->get('payments.fawry.pay_mode'),
            expiry: $config->get('payments.fawry.expiry', 72),
            is_live: $config->get('payments.fawry.is_live'),
            url: $config->get('payments.fawry.url'),
        );
    }

    public function getGateway(): GatewayInterface
    {
        return new FawryGateway($this);
    }

    public function getMode(): bool
    {
        return $this->is_live;
    }

    public function toArray(): array
    {
        return [
            'merchant' => $this->merchant,
            'secret' => $this->secret,
            'returnUrl' => $this->returnUrl,
            'display_mode' => $this->display_mode,
            'pay_mode' => $this->pay_mode,
            'expiry' => $this->expiry,
            'is_live' => $this->is_live,
            'url' => $this->url,
        ];
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }
}
