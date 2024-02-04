# This is my package laravel-payments

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohamedhabibwork/laravel-payments.svg?style=flat-square)](https://packagist.org/packages/mohamedhabibwork/laravel-payments)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mohamedhabibwork/laravel-payments/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mohamedhabibwork/laravel-payments/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mohamedhabibwork/laravel-payments/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mohamedhabibwork/laravel-payments/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mohamedhabibwork/laravel-payments.svg?style=flat-square)](https://packagist.org/packages/mohamedhabibwork/laravel-payments)
[![CodeFactor](https://www.codefactor.io/repository/github/mohamedhabibwork/laravel-payments/badge)](https://www.codefactor.io/repository/github/mohamedhabibwork/laravel-payments)

implementing the payment gateways in laravel (fawry, paymob, tap, hyperpay)

[//]: # (## Support us)

[//]: # ([<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-payments.jpg?t=1" width="419px" />]&#40;https://spatie.be/github-ad-click/laravel-payments&#41;)

[//]: # (We invest a lot of resources into creating [best in class open source packages]&#40;https://spatie.be/open-source&#41;. You can support us by [buying one of our paid products]&#40;https://spatie.be/open-source/support-us&#41;.)

[//]: # (We highly appreciate you sending us a postcard from your hometown, mentioning which of our package&#40;s&#41; you are using. You'll find our address on [our contact page]&#40;https://spatie.be/about-us&#41;. We publish all received postcards on [our virtual postcard wall]&#40;https://spatie.be/open-source/postcards&#41;.)

## Installation

You can install the package via composer:

```bash
composer require mohamedhabibwork/laravel-payments
```

You can publish and run the migrations with:

[//]: # (```bash)

[//]: # (php artisan vendor:publish --tag="laravel-payments-migrations")

[//]: # (php artisan migrate)

[//]: # (```)

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-payments-config"
```

This is the contents of the published config file:

```php
return [
    'paymob' => [
        'api_key' => env('PAYMOB_API_KEY'),
        'currency' => env('PAYMOB_CURRENCY', 'EGP'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        'iframe_id' => env('PAYMOB_IFRAME_ID'),
        'hmac' => env('PAYMOB_HMAC'),
        'wallet_integration_id' => env('PAYMOB_WALLET_INTEGRATION_ID'),
        'wallet_iframe_id' => env('PAYMOB_WALLET_IFRAME_ID'),
        'kiosk_integration_id' => env('PAYMOB_KIOSK_INTEGRATION_ID'),
        'kiosk_iframe_id' => env('PAYMOB_KIOSK_IFRAME_ID'),
        'valu_integration_id' => env('PAYMOB_VALU_INTEGRATION_ID'),
        'valu_iframe_id' => env('PAYMOB_VALU_IFRAME_ID'),
        'cash_integration_id' => env('PAYMOB_CASH_INTEGRATION_ID'),
        'expiration' => env('PAYMOB_EXPIRATION', 36000),
        'is_live' => (bool)env('PAYMOB_IS_LIVE', env('APP_ENV') === 'production'),
    ],
    'fawry' => [
        'merchant' => env('FAWRY_MERCHANT'),
        'secret' => env('FAWRY_SECRET'),
        'returnUrl' => env('FAWRY_RETURN_URL'),
        'display_mode' => env('FAWRY_DISPLAY_MODE', 'POPUP'), //  values [POPUP, INSIDE_PAGE, SIDE_PAGE , SEPARATED]
        'pay_mode' => env('FAWRY_PAY_MODE', 'CARD'), // values ['CashOnDelivery', 'PayAtFawry', 'MWALLET', 'CARD' , 'VALU']
        'expiry' => env('FAWRY_EXPIRY', 72), // hours
        'is_live' => $is_live = (bool)env('FAWRY_IS_LIVE', env('APP_ENV') === 'production'),
        'url' => $is_live ? env('FAWRY_LIVE_URL', 'https://www.atfawry.com') : env('FAWRY_TEST_URL', 'https://atfawry.fawrystaging.com'),
    ],
    'tap' => [
        'is_live' => (bool)env('TAP_IS_LIVE', env('APP_ENV') === 'production'),
        'lang' => env('TAP_LANG', 'auto'), // en, ar, auto
        'test' => [
            'secret' => env('TAP_TEST_SECRET', 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ'),
            'public' => env('TAP_TEST_PUBLIC', 'pk_test_EtHFV4BuPQokJT6jiROls87Y'),
            'currency' => env('TAP_TEST_CURRENCY', 'USD'), // USD, KWD, BHD, SAR, AED, EGP
        ],
        'live' => [
            'secret' => env('TAP_LIVE_SECRET'),
            'public' => env('TAP_LIVE_PUBLIC'),
            'currency' => env('TAP_LIVE_CURRENCY', 'USD'), // USD, KWD, BHD, SAR, AED, EGP
        ],
    ],
    'hyperpay' => [
        'is_live' => $is_live = (bool)env('HYPERPAY_IS_LIVE', env('APP_ENV') === 'production'), // true or false
        'url' => $is_live ? env('HYPERPAY_LIVE_URL', 'https://oppwa.com') : env('HYPERPAY_TEST_URL', 'https://eu-test.oppwa.com'),
        'token' => env('HYPERPAY_TOKEN', 'OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='),
        'credit_id' => env('HYPERPAY_CREDIT_ID', '8a8294174d0595bb014d05d829cb01cd'),
        'mada_id' => env('HYPERPAY_MADA_ID'),
        'applepay_id' => env('HYPERPAY_APPLEPAY_ID'),
        'stcpay_id' => env('HYPERPAY_STCPAY_ID'),
        'currency' => env('HYPERPAY_CURRENCY', 'SAR'),
    ],
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-payments-views"
```

## Usage

```php
$fawry = Habib\LaravelPayments\Facades\FawryFacade::makeUser(id:50,email:"info@habib.cloud",phone:'201********',first_name:,last_name:,language:'ar');
var_dump($fawry->pay(amount: 50,items: [],order_id: 1000,success_url: 'https://success_url.com',failed_url: 'https://failed_url.com'));
=>  [
    'status'=><bool> // true or false
    'url'=><string>  // the payment url
    'orderId' => <string> // the order id
    'message'=><string> // the message
    'data'=><array> // the data of response
    'amount'=><float> // the amount of the payment
    ]
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mohamed Habib](https://github.com/mohamedhabibwork)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
