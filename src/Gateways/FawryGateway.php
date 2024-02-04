<?php

namespace Habib\LaravelPayments\Gateways;

use Habib\LaravelPayments\Configs\FawryConfig;
use Habib\LaravelPayments\Users\FawryUser;
use Illuminate\Support\Facades\Http;

/**
 * Class FawryGateway.
 *
 * @property-read FawryConfig $config
 * @property-read FawryUser $user
 */
class FawryGateway extends Gateway
{
    public function __construct(FawryConfig $config)
    {
        $this->setConfig($config);
        $this->setHttp(
            Http::asJson()->acceptJson()->baseUrl($config->url)
        );
    }

    public function pay(float $amount, array $items = [], ?string $order_id = null, ?string $success_url = null, ?string $failed_url = null): array
    {
        $order_id ??= $this->generateCode();

        if (empty($items)) {
            $items = [
                [
                    'itemId'   => $this->user->id, //item id
                    'quantity' => '1', //item quantity
                    'price'    => number_format($amount, 2, '.', ''), //item price
                ],
            ];
        } else {
            $amount = array_sum(array_map(function ($item) {
                return $item['price'] * $item['quantity'];
            }, $items));
        }

        $fields = [
            'language'          => $this->user->language == 'ar' ? 'ar-eg' : 'en-gb', //ar-eg //en-gb
            'merchantCode'      => $this->config->merchant,
            'merchantRefNum'    => $order_id,
            'customerProfileId' => $this->user->id,
            'chargeItems'       => $items,
            'returnUrl'         => $success_url ?? $failed_url ?? $this->config->returnUrl,
        ];

        $fields['signature'] = $this->generateSignature($fields);

        $response = $this->http->post('/fawrypay-api/api/payments/init', $fields);

        return [
            'status'  => $status = $response->successful(),
            'url'     => $response->body(),
            'orderId' => $order_id,
            'message' => $status ? __('PAYMENT_DONE') : __('PAYMENT_FAILED'),
            'data'    => $fields,
            'amount'  => number_format($amount, 2, '.', ''),
        ];
    }

    /**
     * @param array{merchantCode:string,merchantRefNum:string,customerProfileId:string,returnUrl:string,chargeItems:array{itemId:string,quantity:string,price:string}[],signature:string $fields
     */
    private function generateSignature(array $fields): string
    {
        $signature = $fields['merchantCode'];
        $signature .= $fields['merchantRefNum'];
        $signature .= $fields['customerProfileId'] ?? '';
        $signature .= $fields['returnUrl'];

        foreach ($fields['chargeItems'] as $item) {
            $signature .= $item['itemId'];
            $signature .= $item['quantity'];
            $signature .= $item['price'];
        }

        $signature .= $this->config->secret;

        return hash('sha256', $signature);
    }

    /**
     * @param array{chargeResponse:string} $request
     *
     * @return array|void
     */
    public function verify(array $request)
    {
        if (isset($request['chargeResponse'])) {
            $res = json_decode($request['chargeResponse'], true);
        } else {
            $res = $request;
        }
        $reference_id = $res['merchantRefNumber'];

        $hash = hash('sha256', $this->config->merchant.$reference_id.$this->config->secret);

        $response = $this->http->get('ECommerceWeb/Fawry/payments/status?'.http_build_query([
            'merchantCode'      => $this->config->merchant,
            'merchantRefNumber' => $reference_id,
            'signature'         => $hash,
        ]));

        if ($response->offsetGet('statusCode') == 200 && $response->offsetGet('paymentStatus') == 'PAID') {
            return [
                'success'      => true,
                'payment_id'   => $reference_id,
                'message'      => __('PAYMENT_DONE'),
                'process_data' => $request,
            ];
        } elseif ($response->offsetGet('statusCode') != 200) {
            return [
                'success'    => false,
                'payment_id' => $reference_id,
                'message'    => __('PAYMENT_FAILED'),
                'data'       => $request,
            ];
        }
    }

    public function makeUser(
        ?string $id = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $language = 'en',
    ): self {
        return $this->setUser(new FawryUser(
            id: $id ?? $this->generateCode('user'),
            email: $email ?? '',
            phone: $phone ?? '',
            first_name: $first_name ?? '',
            last_name: $last_name ?? '',
            language: $language,
        ));
    }
}
