<?php

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Key From MARASOFTPAY Dashboard
     *
     */
    'publicKey' => getenv('MARASOFTPAY_PUBLIC_KEY'),

    /**
     * Encryption Key From MARASOFTPAY Dashboard
     *
     */
    'encryptionKey' => getenv('MARASOFTPAY_ENCRYPTION_KEY'),

    /**
     * You enviroment can either be live or stage.
     * Make sure to add the appropriate API key after changing the enviroment in .env
     *
     */
    'env' => env('MARASOFTPAY_ENV', 'test'), // OR "LIVE"

    /**
     * Your secret hash is a unique key which is part of the data sent with your webhooks
     * It serves as a form of verification to prove that the webhook is coming from Marasoft Pay.
     *
     */
    'hash' => env('MARASOFTPAY_HASH', 'MarasoftPay'), // OR "LIVE"


    /**
     * MARASOFTPAY Base URL
     *
     */
    'user_bear_charge' => env('MARASOFTPAY_USER_BEAR_CHARGE', "yes"),


    /**
     * MARASOFTPAY Base URL
     *
     */
    'baseUrl' => env('MARASOFTPAY_LIVE_URL', "https://api.marasoftpay.live"),

];