<?php

namespace Habib\LaravelPayments\Configs;

use Habib\LaravelPayments\Gateways\GatewayInterface;
use Habib\LaravelPayments\Gateways\TapGateway;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class TapConfig implements ConfigInterface, Arrayable,Jsonable
{
    public string $url = 'https://api.tap.company/v2/';

    public function __construct(
        public string $secret,
        public string $public,
        public string $currency,
        public string $lang,
        public bool   $is_live = false,
    )
    {

    }

    public function getGateway(): GatewayInterface
    {
        return new TapGateway($this);
    }

    public function getMode(): bool
    {
        return $this->is_live;
    }

    public static function initConfig(Repository $config): ConfigInterface
    {
        $is_live = $config->get('payments.tap.is_live');
        return new self(
            secret: $is_live ? $config->get('payments.tap.live.secret') : $config->get('payments.tap.test.secret'),
            public: $is_live ? $config->get('payments.tap.live.public') : $config->get('payments.tap.test.public'),
            currency: $is_live ? $config->get('payments.tap.live.currency') : $config->get('payments.tap.test.currency'),
            lang: $config->get('payments.tap.lang'),
            is_live: $is_live,
        );
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'secret' => $this->secret,
            'public' => $this->public,
            'currency' => $this->currency,
            'lang' => $this->lang,
            'is_live' => $this->is_live,
        ];
    }


    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }
}
