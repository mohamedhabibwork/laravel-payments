<?php

namespace Habib\LaravelPayments\Gateways;

use Habib\LaravelPayments\Configs\PaymobConfig;
use Habib\LaravelPayments\Users\PaymobUser;
use Illuminate\Support\Facades\Http;

/**
 * Class PaymobGateway.
 *
 * @property-read PaymobConfig $config
 *
 * @see https://docs.paymob.com/docs/accept-standard-redirect
 * @see https://docs.paymob.com/docs/card-payments
 * @see https://docs.paymob.com/docs/kiosk-payments
 * @see https://docs.paymob.com/docs/mobile-wallets
 * @see https://docs.paymob.com/docs/valu
 */
class PaymobGateway extends Gateway
{
    public function __construct(PaymobConfig $config)
    {
        $this->setHttp(
            Http::acceptJson()->asJson()->baseUrl('https://accept.paymobsolutions.com/api/')
        );

        $this->setConfig($config);
    }

    public function payWallet(float $amount, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $amount *= 100;

        $authToken = $this->getAuthToken();

        $paymentToken = $this->getPaymentKey($authToken, $amount, $this->config->wallet_integration_id, $order_id, $subscription_plan_id, $billing_data, $items);

        $response = $this->http->post('acceptance/payments/pay', [
            'source' => [
                'identifier' => $this->config->is_live ? $this->user->phone : '01010101010',
                'subtype'    => 'WALLET',
            ],
            'payment_token' => $paymentToken['token'],
        ]);

        return [
            'status'   => $status = $response->successful(),
            'message'  => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'orderId'  => $paymentToken['order_id'],
            'url'      => $response->json('redirect_url'),
            'amount'   => $amount / 100,
            'currency' => $this->config->currency,
            'data'     => $response->json(),
        ];
    }

    public function getAuthToken(): string
    {
        $response = $this->http->post('auth/tokens', [
            'api_key' => $this->config->api_key,
        ]);

        return $response->json('token');
    }

    public function getPaymentKey(string $token, float $amount, string $integration_id, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $order = $this->makeOrder($token, $amount, $order_id, $items);

        $billing_data = array_merge($billing_data ?? [], [
            'apartment'       => 'NA',
            'email'           => $this->user->email,
            'floor'           => 'NA',
            'first_name'      => $this->user->first_name,
            'street'          => 'NA',
            'building'        => 'NA',
            'phone_number'    => $this->user->phone,
            'shipping_method' => 'NA',
            'postal_code'     => 'NA',
            'city'            => 'NA',
            'country'         => 'NA',
            'last_name'       => $this->user->last_name,
            'state'           => 'NA',
        ]);

        $request = [
            'auth_token'           => $token,
            'expiration'           => $this->config?->expiration ?? 36000,
            'amount_cents'         => $order['amount_cents'] ?? $amount,
            'order_id'             => $order['id'],
            'billing_data'         => $billing_data,
            'currency'             => $this->config->currency,
            'integration_id'       => $integration_id,
            'lock_order_when_paid' => true,
        ];

        if ($subscription_plan_id) {
            $request['subscription_id'] = $subscription_plan_id;
        }

        $response = $this->http->post('acceptance/payment_keys', $request);

        return [
            'token'    => $response->json('token'),
            'order_id' => $order['id'] ?? '',
        ];
    }

    public function makeOrder(string $token, float $amount, ?string $merchant_order_id = null, array $items = []): array
    {
        $merchant_order_id ??= uniqid($this->generateCode().'-');

        $response = $this->http->post('ecommerce/orders', [
            'auth_token'        => $token,
            'delivery_needed'   => false,
            'amount_cents'      => $amount,
            'currency'          => $this->config->currency,
            'merchant_order_id' => $merchant_order_id,
            'items'             => $items,
        ]);

        return $response->json();
    }

    public function payCash(float $amount, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $amount *= 100;

        $authToken = $this->getAuthToken();

        $paymentToken = $this->getPaymentKey($authToken, $amount, $this->config->cash_integration_id, $order_id, $subscription_plan_id, $billing_data, $items);

        $response = $this->http->post('acceptance/payments/pay', [
            'source' => [
                'identifier' => 'cash',
                'subtype'    => 'CASH',
            ],
            'payment_token' => $paymentToken['token'],
        ]);

        return [
            'status'   => $status = $response->successful(),
            'message'  => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'orderId'  => $paymentToken['order_id'],
            'url'      => $response->json('redirect_url'),
            'amount'   => $amount / 100,
            'currency' => $this->config->currency,
            'data'     => $response->json(),
        ];
    }

    public function payKiosk(float $amount, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $amount *= 100;

        $authToken = $this->getAuthToken();

        $paymentToken = $this->getPaymentKey($authToken, $amount, $this->config->wallet_integration_id, $order_id, $subscription_plan_id, $billing_data, $items);

        $response = $this->http->post('acceptance/payments/pay', [
            'source' => [
                'identifier' => 'AGGREGATOR',
                'subtype'    => 'AGGREGATOR',
            ],
            'payment_token' => $paymentToken['token'],
        ]);

        return [
            'status'   => $status = $response->successful(),
            'message'  => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'orderId'  => $paymentToken['order_id'],
            'url'      => $response->json('redirect_url'),
            'amount'   => $amount / 100,
            'currency' => $this->config->currency,
            'data'     => $response->json(),
        ];
    }

    public function pay(float $amount, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $amount *= 100;

        $authToken = $this->getAuthToken();

        $paymentToken = $this->getPaymentKey($authToken, $amount, $this->config->integration_id, $order_id, $subscription_plan_id, $billing_data, $items);

        return [
            'order_id'     => $paymentToken['order_id'],
            'redirect_url' => "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->config->iframe_id}?payment_token={$paymentToken['token']}",
            'amount'       => $amount / 100,
            'currency'     => $this->config->currency,
        ];
    }

    public function payValU(float $amount, ?string $order_id = null, ?string $subscription_plan_id = null, ?array $billing_data = null, array $items = []): array
    {
        $amount *= 100;

        $authToken = $this->getAuthToken();

        $paymentToken = $this->getPaymentKey($authToken, $amount, $this->config->valu_integration_id, $order_id, $subscription_plan_id, $billing_data, $items);

        return [
            'orderId'  => $paymentToken['order_id'],
            'url'      => "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->config->valu_iframe_id}?payment_token={$paymentToken['token']}",
            'amount'   => $amount / 100,
            'currency' => $this->config->currency,
        ];
    }

    /**
     * @return array{url: string, id: string}
     */
    public function linkCreation(float $amount, string $product_name, string $product_description, ?array $integrations = null): array
    {
        $amount *= 100;
        $integrations ??= [
            $this->config->valu_integration_id,
            $this->config->kiosk_integration_id,
            $this->config->wallet_integration_id,
            $this->config->integration_id,
        ];

        $authToken = $this->getAuthToken();

        $response = $this->http->post('ecommerce/products', [
            'auth_token'          => $authToken,
            'product_name'        => $product_name,
            'amount_cents'        => $amount,
            'currency'            => $this->config->currency,
            'inventory'           => '1',
            'delivery_needed'     => 'false',
            'integrations'        => array_values(array_filter($integrations)),
            'allow_quantity_edit' => 'false',
            'product_description' => $product_description,
        ]);

        return [
            'status'  => $status = $response->successful(),
            'message' => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'id'      => $response->json('id'),
            'url'     => $response->json('product_url'),
        ];
    }

    /**
     * @param array{amount_cents: string, created_at: string, currency: string, error_occured: string, has_parent_transaction: string, id: string, integration_id: string, is_3d_secure: string, is_auth: string, is_capture: string, is_refunded: string, is_standalone_payment: string, is_voided: string, order: string, owner: string, pending: string, source_data_pan: string, source_data_sub_type: string, source_data_type: string, success: string} $data
     */
    public function verify(array $data): array
    {
        $keys = ['amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'obj.id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success'];

        $string = '';
        foreach ($keys as $key) {
            $string .= (string) data_get($data, $key);
        }

        if (hash_hmac('sha512', $string, $this->config->hmac)) {
            return [
                'status'  => true,
                'message' => 'Verified',
            ];
        }

        return [
            'status'  => false,
            'message' => match (request()->string('txn_response_code')) {
                'BLOCKED', 'B' => __('Process Has Been Blocked From System'),
                '5', '6051' => __('Balance is not enough'),
                'F'     => __('Your card is not authorized with 3D secure'),
                '7'     => __('Incorrect card expiration date'),
                '2'     => __('Declined'),
                '637'   => __('The OTP number was entered incorrectly'),
                '11'    => __('Security checks are not passed by the system'),
                default => __('An error occurred while executing the operation'),
            },
        ];
    }

    public function makeUser(
        ?string $email = null,
        ?string $phone = null,
        ?string $first_name = null,
        ?string $last_name = null,
    ): self {
        return $this->setUser(new PaymobUser(
            email: $email ?? '',
            phone: $phone ?? '',
            first_name: $first_name ?? '',
            last_name: $last_name ?? '',
        ));
    }
}
