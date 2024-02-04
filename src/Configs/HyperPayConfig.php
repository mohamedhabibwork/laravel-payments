<?php

namespace Habib\LaravelPayments\Configs;

use Config;
use Habib\LaravelPayments\Gateways\GatewayInterface;
use Habib\LaravelPayments\Gateways\HyperPayGateway;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class HyperPayConfig extends Config implements ConfigInterface, Arrayable, Jsonable
{
    public function __construct(
        public string  $url,
        public string  $token,
        public string  $credit_entity_id,
        public ?string $mada_entity_id = null,
        public ?string $applepay_entity_id = null,
        public ?string $stcpay_entity_id = null,
        public ?string $currency = 'SAR',
        public bool    $is_live = false,
    )
    {
    }

    public static function initConfig(Repository $config): ConfigInterface
    {
        return new self(
            url: $config->get('payments.hyperpay.url'),
            token: $config->get('payments.hyperpay.token'),
            credit_entity_id: $config->get('payments.hyperpay.credit_id'),
            mada_entity_id: $config->get('payments.hyperpay.mada_id'),
            applepay_entity_id: $config->get('payments.hyperpay.applepay_id'),
            stcpay_entity_id: $config->get('payments.hyperpay.stcpay_id'),
            currency: $config->get('payments.hyperpay.currency'),
            is_live: $config->get('payments.hyperpay.is_live'),
        );
    }

    public function getGateway(): GatewayInterface
    {
        return new HyperPayGateway($this);
    }

    public function getMode(): bool
    {
        return $this->is_live;
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'token' => $this->token,
            'credit_entity_id' => $this->credit_entity_id,
            'mada_entity_id' => $this->mada_entity_id,
            'applepay_entity_id' => $this->applepay_entity_id,
            'stcpay_entity_id' => $this->stcpay_entity_id,
            'currency' => $this->currency,
            'is_live' => $this->is_live,
        ];
    }
}
