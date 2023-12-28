<?php

namespace PrevailExcel\MarasoftPay;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MarasoftPayServiceProvider extends ServiceProvider
{

    /*
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../utils/config/marasoftpay.php');

        $this->publishes([
            $config => config_path('marasoftpay.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../utils/config/marasoftpay.php',
            'marasoftpay'
        );
        if (File::exists(__DIR__ . '/../utils/helpers/marasoftpay.php')) {
            require __DIR__ . '/../utils/helpers/marasoftpay.php';
        }

        /**
         * @param  array|string $controller
         * @param  string|null  $class
         * */
        Route::macro('MCallback', function ($controller, string $class = 'handleGatewayCallback') {
            return Route::any('marasoftpay/callback', [$controller, $class])->name("marasoftpay.lara.callback");
        });
        Route::macro('MWebhook', function ($controller, string $class = 'handleWebhook') {
            return Route::post('marasoftpay/webhook', [$controller, $class])->name("marasoftpay.lara.webhook");
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind('laravel-marasoftpay', function () {
            return new MarasoftPay;
        });
    }

    /**
     * Get the services provided by the provider
     * @return array
     */
    public function provides()
    {
        return ['laravel-marasoftpay'];
    }
}
