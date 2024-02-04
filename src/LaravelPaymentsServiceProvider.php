<?php

namespace Habib\LaravelPayments;

use Habib\LaravelPayments\Configs\FawryConfig;
use Habib\LaravelPayments\Configs\HyperPayConfig;
use Habib\LaravelPayments\Configs\KashierConfig;
use Habib\LaravelPayments\Configs\OpayConfig;
use Habib\LaravelPayments\Configs\PaymobConfig;
use Habib\LaravelPayments\Configs\TapConfig;
use Habib\LaravelPayments\Gateways\FawryGateway;
use Habib\LaravelPayments\Gateways\HyperPayGateway;
use Habib\LaravelPayments\Gateways\KashierGateway;
use Habib\LaravelPayments\Gateways\OpayGateway;
use Habib\LaravelPayments\Gateways\PaymobGateway;
use Habib\LaravelPayments\Gateways\TapGateway;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPaymentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-payments')
            ->hasConfigFile()
            ->hasViews()
//            ->hasMigration('create_payments_table')
//            ->hasCommand(LaravelPaymentsCommand::class)
        ;

        $this->app->bind(HyperPayGateway::class, fn($app) => HyperPayConfig::initConfig($app['config'])->getGateway());
        $this->app->bind(TapGateway::class, fn($app) => TapConfig::initConfig($app['config'])->getGateway());
        $this->app->bind(FawryGateway::class, fn($app) => FawryConfig::initConfig($app['config'])->getGateway());
        $this->app->bind(PaymobGateway::class, fn($app) => PaymobConfig::initConfig($app['config'])->getGateway());
    }
}
