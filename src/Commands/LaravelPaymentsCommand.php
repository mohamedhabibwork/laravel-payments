<?php

namespace Habib\LaravelPayments\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'laravel-payments',
    description: 'My command',
)]
class LaravelPaymentsCommand extends Command
{
    public $signature = 'laravel-payments';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
