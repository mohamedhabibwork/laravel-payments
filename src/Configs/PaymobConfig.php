<?php

namespace Habib\LaravelPayments\Configs;

use Habib\LaravelPayments\Gateways\PaymobGateway;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class PaymobConfig implements ConfigInterface, Arrayable,Jsonable
{
    public function __construct(
        public string $api_key,
        public string $currency,
        public string $integration_id,
        public string $iframe_id,
        public string $hmac,
        public ?string $wallet_integration_id = null,
        public ?string $wallet_iframe_id = null,
        public ?string $kiosk_integration_id = null,
        public ?string $kiosk_iframe_id = null,
        public ?string $valu_integration_id = null,
        public ?string $valu_iframe_id = null,
        public ?string $cash_integration_id = null,
        public int $expiration = 36000,
        public bool $is_live = false,
    ) {
    }

    public function getGateway(): PaymobGateway
    {
        return new PaymobGateway($this);
    }

    public function getMode(): bool
    {
        return $this->is_live;
    }

    public static function initConfig(\Illuminate\Config\Repository $config): self
    {
        return new self(
            api_key: $config->get('payments.paymob.api_key'),
            currency: $config->get('payments.paymob.currency'),
            integration_id: $config->get('payments.paymob.integration_id'),
            iframe_id: $config->get('payments.paymob.iframe_id'),
            hmac: $config->get('payments.paymob.hmac'),
            wallet_integration_id: $config->get('payments.paymob.wallet_integration_id'),
            wallet_iframe_id: $config->get('payments.paymob.wallet_iframe_id'),
            kiosk_integration_id: $config->get('payments.paymob.kiosk_integration_id'),
            kiosk_iframe_id: $config->get('payments.paymob.kiosk_iframe_id'),
            valu_integration_id: $config->get('payments.paymob.valu_integration_id'),
            valu_iframe_id: $config->get('payments.paymob.valu_iframe_id'),
            cash_integration_id: $config->get('payments.paymob.cash_integration_id'),
            expiration: $config->get('payments.paymob.expiration'),
            is_live: $config->get('payments.paymob.is_live'),
        );
    }

    public function toArray(): array
    {
        return [
            'api_key' => $this->api_key,
            'currency' => $this->currency,
            'integration_id' => $this->integration_id,
            'iframe_id' => $this->iframe_id,
            'hmac' => $this->hmac,
            'wallet_integration_id' => $this->wallet_integration_id,
            'wallet_iframe_id' => $this->wallet_iframe_id,
            'kiosk_integration_id' => $this->kiosk_integration_id,
            'kiosk_iframe_id' => $this->kiosk_iframe_id,
            'valu_integration_id' => $this->valu_integration_id,
            'valu_iframe_id' => $this->valu_iframe_id,
            'cash_integration_id' => $this->cash_integration_id,
            'expiration' => $this->expiration,
            'is_live' => $this->is_live,
        ];
    }
    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }
}
