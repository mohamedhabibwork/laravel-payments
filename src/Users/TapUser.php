<?php

namespace Habib\LaravelPayments\Users;

class TapUser extends PaymentUser
{
    public function __construct(
        public string $email,
        public string $phone,
        public string $first_name,
        public string $last_name,
        public string $country_code,
    ) {
    }
}
