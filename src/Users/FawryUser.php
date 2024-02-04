<?php

namespace Habib\LaravelPayments\Users;

class FawryUser extends PaymentUser
{
    public function __construct(
        public string $id,
        string $email,
        string $phone,
        string $first_name,
        string $last_name,
        public string $language = 'ar',
    ) {
        parent::__construct(
            email: $email,
            phone: $phone,
            first_name: $first_name,
            last_name: $last_name,
        );
    }
}
