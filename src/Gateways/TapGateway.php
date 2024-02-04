<?php

namespace Habib\LaravelPayments\Gateways;

use Habib\LaravelPayments\Configs\TapConfig;
use Habib\LaravelPayments\Users\TapUser;
use Illuminate\Support\Facades\Http;

/**
 * @property-read TapConfig $config
 * @property-read TapUser $user
 */
class TapGateway extends Gateway
{
    public function __construct(TapConfig $config)
    {
        $this->setConfig($config);
        $this->setHttp(
            Http::acceptJson()
                ->asJson()
                ->withToken($this->config->secret)
                ->withHeaders([
                    'lang_code' => $this->config->lang == 'auto' ? app()->getLocale() : $this->config->lang,
                ])
                ->baseUrl($this->config->url)
        );
    }

    public function pay(float $amount, string $post_url, string $redirect_url, ?string $source = 'src_all', array $options = [])
    {
        $orderId = $options['orderId'] ?? $this->generateCode();

        $response = $this->http->post('charges', [
            'amount'               => $amount,
            'currency'             => $this->config->currency,
            'customer_initiated'   => $options['customer_initiated'] ?? true,
            'threeDSecure'         => $options['threeDSecure'] ?? true,
            'save_card'            => $options['save_card'] ?? false,
            'authorize_debit'      => $options['authorize_debit'] ?? false,
            'description'          => $options['description'] ?? '',
            'metadata'             => $options['metadata'] ?? [],
            'statement_descriptor' => $options['statement_descriptor'] ?? '',
            'reference'            => [
                'transaction' => $options['transactionId'] ?? $orderId,
                'order'       => $orderId,
            ],
            'receipt' => [
                'email' => $options['receipt_email'] ?? true,
                'sms'   => $options['receipt_sms'] ?? false,
            ], 'customer' => [
                'first_name'  => $this->user->first_name,
                'middle_name' => '',
                'last_name'   => $this->user->last_name,
                'email'       => $this->user->email,
                'phone'       => [
                    'country_code' => $this->user->country_code,
                    'number'       => $this->user->phone,
                ],
            ],
            'source'   => ['id' => $source ?? 'src_all'],
            'post'     => ['url' => $post_url],
            'redirect' => ['url' => $redirect_url],
        ]);

        $data = $response->json();

        return [
            'status'      => $response->successful() && ($data['status'] ?? '') == 'INITIATED',
            'orderId'     => $orderId,
            'checkoutId'  => $data['id'] ?? '',
            'checkoutUrl' => $data['transaction']['url'] ?? '',
            'merchantId'  => $data['merchant']['id'] ?? '',
            'data'        => $data,
            'brand'       => $data['transaction']['payment_method']['name'] ?? '',
        ];
    }

    public function verify(string $checkoutId): array
    {
        $response = $this->http->get("charges/$checkoutId");

        $data = $response->json();

        $status = (isset($data['status']) && $data['status'] == 'CAPTURED');

        return [
            'status'               => $status,
            'orderId'              => $data['order']['id'] ?? '',
            'referenceTransaction' => $data['reference']['transaction'] ?? '',
            'referenceOrder'       => $data['reference']['order'] ?? '',
            'checkoutId'           => $data['id'] ?? '',
            'customerId'           => $data['customer']['id'] ?? '',
            'merchantId'           => $data['merchant']['id'] ?? '',
            'card'                 => $data['card'] ?? [],
            'source'               => $data['source'] ?? [],
            'data'                 => $data,
        ];
    }

    public function makeUser(
        ?string $email = null,
        ?string $country_code = null,
        ?string $phone = null,
        ?string $first_name = null,
        ?string $last_name = null,
    ): self {
        return $this->setUser(new TapUser(
            email: $email ?? '',
            phone: $phone ?? '',
            first_name: $first_name ?? '',
            last_name: $last_name ?? '',
            country_code: $country_code ?? '',
        ));
    }
}
