<?php

namespace Habib\LaravelPayments\Gateways;

use Closure;
use Habib\LaravelPayments\Configs\HyperPayConfig;
use Habib\LaravelPayments\Users\HyperPayUser;
use Illuminate\Support\Facades\Http;

/**
 * @property-read HyperPayConfig $config
 * @property-read HyperPayUser $user
 */
class HyperPayGateway extends Gateway
{
    protected static ?Closure $guessBrand = null;

    protected static ?Closure $guessEntity = null;

    /**
     * @description HyperPayGateway constructor.
     *
     * @see https://hyperpay.docs.oppwa.com/tutorials/integration-guide
     */
    public function __construct(HyperPayConfig $config)
    {
        $this->setConfig($config);
        $this->setHttp(
            Http::acceptJson()
                ->withToken($this->config->token)
                ->baseUrl($this->config->url)
        );
    }

    public function setGuessBrand(Closure $guessBrand): self
    {
        self::usingGuessBrand($guessBrand);

        return $this;
    }

    public static function usingGuessBrand(Closure $guessBrand): void
    {
        static::$guessBrand = $guessBrand;
    }

    public function setGuessEntityId(Closure $guessEntityId): self
    {
        self::usingGuessEntityId($guessEntityId);

        return $this;
    }

    public static function usingGuessEntityId(Closure $guessEntityId): void
    {
        static::$guessEntity = $guessEntityId;
    }

    /**
     * @param array $options {order_id,success_url,failed_url}
     *
     * @return array{checkoutId:string,paymentBrand:string,result_code:string,status:bool,orderId:string,checkoutUrl:string,brand:string}
     */
    public function pay(float $amount, string $paymentBrand, array $options = []): array
    {
        $entityId = $options['entityId'] ?? $this->guessEntityId($paymentBrand);
        $orderId = $options['orderId'] ?? $this->generateCode('order-'.str($paymentBrand)->slug()->toString());
        $request = [
            'entityId'              => $entityId,
            'amount'                => $amount,
            'currency'              => $options['currency'] ?? $this->config->currency,
            'paymentType'           => $options['paymentType'] ?? 'DB',
            'merchantTransactionId' => $orderId,
            'billing.street1'       => $options['street1'] ?? 'riyadh',
            'billing.city'          => $options['city'] ?? 'riyadh',
            'billing.state'         => $options['state'] ?? 'riyadh',
            'billing.country'       => $options['country'] ?? 'SA',
            'billing.postcode'      => $options['postcode'] ?? '123456',
            'customer.email'        => $this->user->email,
            'customer.givenName'    => $this->user->first_name,
            'customer.surname'      => $this->user->last_name,
        ];

        if ($options['createRegistration'] ?? false) {
            $request['createRegistration'] = $options['createRegistration'];
        }
        if ($options['testMode'] ?? false) {
            $request['testMode'] = $options['testMode'];
        }

        if (!$this->config->is_live) {
            $request['customParameters[3DS2_enrolled]'] = 'true';  // remove it in live
        }

        foreach ($options['registrations'] ?? [] as $key => $token) {
            $request['registrations['.$key.'].id'] = $token;
        }

        $response = $this->http->asForm()->post('v1/checkouts', $request);
        $checkoutId = $response->json('id');

        return [
            'brand'        => $this->guessBrand($paymentBrand),
            'status'       => $status = $this->isSuccess($response->json('result.code')),
            'message'      => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'orderId'      => $orderId,
            'url'          => trim($this->config->url, '/')."/v1/paymentWidgets.js?checkoutId={$checkoutId}",
            'checkoutId'   => $checkoutId,
            'paymentBrand' => $paymentBrand,
            'result_code'  => $response->json('result.code'),
            'data'         => $request,
            'amount'       => number_format($amount, 2, '.', ''),
        ];
    }

    /**
     * @description guess entity id from source
     */
    public function guessEntityId(string $source): string
    {
        if (static::$guessEntity instanceof Closure) {
            return call_user_func(static::$guessEntity, $source);
        }

        return match (mb_strtolower($source)) {
            //            'visa', 'mastercard', 'amex', 'visa master' => $this->config->credit_entity_id,
            'mada'     => $this->config->mada_entity_id,
            'applepay' => $this->config->applepay_entity_id,
            'stcpay'   => $this->config->stcpay_entity_id,
            default    => $this->config->credit_entity_id,
        } ?? $this->config->credit_entity_id;
    }

    public function isSuccess(string $code): bool
    {
        return match ($code) {
            '000.000.000', '000.000.100', '000.100.110',
            '000.100.111', '000.100.112', '000.300.000',
            '000.300.100', '000.300.101', '000.300.102',
            '000.600.000', '000.200.100', => true,
            default => false,
        };
    }

    /**
     * @description guess brand from source
     */
    public function guessBrand(string $source): string
    {
        if (static::$guessBrand instanceof Closure) {
            return call_user_func(static::$guessBrand, $source);
        }

        return match (mb_strtolower($source)) {
            'mada'     => 'MADA',
            'applepay' => 'APPLEPAY',
            'stcpay'   => 'STCPAY',
            'visa', 'mastercard', 'visa master' => 'VISA MASTER',
            'amex'  => 'AMEX',
            'meeza' => 'MEEZA',
            default => mb_strtoupper($source),
        };
    }

    public function getPayment($checkoutId, string $paymentBrand)
    {
        $entityId = $this->guessEntityId($paymentBrand);
        $response = $this->http->get("v1/checkouts/{$checkoutId}/payment", compact('entityId'));

        return [
            'status'      => $this->isSuccess($response->json('result.code')),
            'result_code' => $response->json('result.code'),
            'brand'       => $paymentBrand,
            'checkoutId'  => $response->json('id'),
            'data'        => $response->json(),
        ];
    }

    public function getPaymentByResource($resourcePath, string $paymentBrand): array
    {
        $entityId = $this->guessEntityId($paymentBrand);
        $response = $this->http->get($resourcePath, compact('entityId'));

        return [
            'status'      => $this->isSuccess($response->json('result.code')),
            'result_code' => $response->json('result.code'),
            'brand'       => $paymentBrand,
            'checkoutId'  => $response->json('id'),
            'data'        => $response->json(),
        ];
    }

    public function makeUser(
        ?string $email = null,
        ?string $phone = null,
        ?string $first_name = null,
        ?string $last_name = null,
    ): self {
        return $this->setUser(new HyperPayUser(
            email: $email ?? '',
            phone: $phone ?? '',
            first_name: $first_name ?? '',
            last_name: $last_name ?? '',
        ));
    }
}
