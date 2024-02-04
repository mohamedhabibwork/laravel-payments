<?php

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
        'is_live' => (bool) env('PAYMOB_IS_LIVE', env('APP_ENV') === 'production'),
    ],
    'fawry' => [
        'merchant' => env('FAWRY_MERCHANT'),
        'secret' => env('FAWRY_SECRET'),
        'returnUrl' => env('FAWRY_RETURN_URL'),
        'display_mode' => env('FAWRY_DISPLAY_MODE', 'POPUP'), //  values [POPUP, INSIDE_PAGE, SIDE_PAGE , SEPARATED]
        'pay_mode' => env('FAWRY_PAY_MODE', 'CARD'), // values ['CashOnDelivery', 'PayAtFawry', 'MWALLET', 'CARD' , 'VALU']
        'expiry' => env('FAWRY_EXPIRY', 72), // hours
        'is_live' => $is_live = (bool) env('FAWRY_IS_LIVE', env('APP_ENV') === 'production'),
        'url' => $is_live ? env('FAWRY_LIVE_URL', 'https://www.atfawry.com') : env('FAWRY_TEST_URL', 'https://atfawry.fawrystaging.com'),
    ],
    'tap' => [
        'is_live' => (bool) env('TAP_IS_LIVE', env('APP_ENV') === 'production'),
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
        'is_live' => $is_live = (bool) env('HYPERPAY_IS_LIVE', env('APP_ENV') === 'production'), // true or false
        'url' => $is_live ? env('HYPERPAY_LIVE_URL', 'https://oppwa.com') : env('HYPERPAY_TEST_URL', 'https://eu-test.oppwa.com'),
        'token' => env('HYPERPAY_TOKEN', 'OGE4Mjk0MTc0ZDA1OTViYjAxNGQwNWQ4MjllNzAxZDF8OVRuSlBjMm45aA=='),
        'credit_id' => env('HYPERPAY_CREDIT_ID', '8a8294174d0595bb014d05d829cb01cd'),
        'mada_id' => env('HYPERPAY_MADA_ID'),
        'applepay_id' => env('HYPERPAY_APPLEPAY_ID'),
        'stcpay_id' => env('HYPERPAY_STCPAY_ID'),
        'currency' => env('HYPERPAY_CURRENCY', 'SAR'),
    ],
];
