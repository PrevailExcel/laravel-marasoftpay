<?php

namespace PrevailExcel\MarasoftPay\Facades;

use Illuminate\Support\Facades\Facade;

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MarasoftPay extends Facade
{
    /**
     * Get the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-marasoftpay';
    }
    
}