<?php

namespace Habib\LaravelPayments\Users;

class PaymentUser implements UserInterface
{
    public function __construct(
        public string $email,
        public string $phone,
        public string $first_name,
        public string $last_name,
    ) {
    }

    public function name()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
