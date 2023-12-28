# laravel-marasoftpay

[![Latest Stable Version](https://poser.pugx.org/prevailexcel/laravel-marasoftpay/v/stable.svg)](https://packagist.org/packages/prevailexcel/laravel-marasoftpay)
[![License](https://poser.pugx.org/prevailexcel/laravel-marasoftpay/license.svg)](LICENSE.md)
> A Laravel Package for working with MarasoftPay Payments seamlessly.

Collect payments from individuals or businesses locally and globally, and settle them in multiple currencies, ensuring a cost-effective and hassle-free payment process.
This package also allows you to receive all types of webhooks from [Marasoft Pay](https://marasoftpay.com/) which it verifies and handles the payloads for you. You can start collecting payment in payments in minutes.

    Bank Transfers
    USSD
    Cards
    Virtual Bank Accounts
    Mobile Money

## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Marasoft Pay, simply require it

```bash
composer require prevailexcel/laravel-marasoftpay
```

Or add the following line to the require block of your `composer.json` file.

```
"prevailexcel/laravel-marasoftpay": "1.0.*"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Marasoft Pay is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key. 
> If you use **Laravel >= 5.5** you can skip this step and go to [**`configuration`**](https://github.com/PrevailExcel/laravel-marasoftpay#configuration)

```php
'providers' => [
    ...
    PrevailExcel\MarasoftPay\MarasoftPayServiceProvider::class,
    ...
]
```

Also, register the Facade like so:

```php
'aliases' => [
    ...
    'MarasoftPay' => PrevailExcel\MarasoftPay\Facades\MarasoftPay::class,
    ...
]
```

## Configuration

You can publish the configuration file using this command:

```bash
php artisan vendor:publish --provider="PrevailExcel\MarasoftPay\MarasoftPayServiceProvider"
```

A configuration-file named `marasoftpay.php` with some sensible defaults will be placed in your `config` directory:

```php
<?php

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
    'baseUrl' => env('MARASOFTPAY_LIVE_URL', "https://api.marasoftpay.live"),
];
```

## General payment flow

Though there are multiple ways to pay an order, most payment gateways expect you to follow the following flow in your checkout process:

### 1. The customer is redirected to the payment provider
After the customer has gone through the checkout process and is ready to pay, the customer must be redirected to the site of the payment provider.

The redirection is accomplished by submitting a form with some hidden fields. The form must send a POST request to the site of the payment provider. The hidden fields minimally specify the amount that must be paid, the order id and a hash.

The hash is calculated using the hidden form fields and a non-public secret. The hash used by the payment provider to verify if the request is valid.


### 2. The customer pays on the site of the payment provider
The customer arrives on the site of the payment provider and gets to choose a payment method. All steps necessary to pay the order are taken care of by the payment provider.

### 3. The customer gets redirected back to your site
After having paid the order the customer is redirected back. In the redirection request to the shop-site some values are returned. The values are usually the order id, a payment result and a hash.

The hash is calculated out of some of the fields returned and a secret non-public value. This hash is used to verify if the request is valid and comes from the payment provider. It is paramount that this hash is thoroughly checked.

## Usage

Open your .env file and add all the necessary keys like so:

```bash
MARASOFTPAY_PUBLIC_KEY=MSFT_****_****************************************
MARASOFTPAY_ENCRYPTION_KEY=MSFT_Enc******************************************
MARASOFTPAY_ENV=test
MARASOFTPAY_HASH=yoursecret
```
*If you are using a hosting service like heroku, ensure to add the above details to your configuration variables.*
*Remember to change MARASOFTPAY_ENV to 'live' and update the keys when you are in production*

#### Next, you have to setup your routes. 
There are 3 routes you should have to get started.
1. To initiate payment
2. To setup callback - Route::callback.
3. To setup webhook and handle the event responses - Route::webhook.

```php
// Laravel 5.1.17 and above
Route::post('/pay', 'PaymentController@createPayment')->name('pay');
Route::callback(PaymentController::class, 'handleGatewayCallback');
Route::webhook(WebhookController::class, 'handleWebhook');
```
OR

```php
// Laravel 8 & 9
Route::post('/pay', [PaymentController::class, 'createPayment'])->name('pay');
Route::callback(PaymentController::class, 'handleGatewayCallback');
Route::webhook(WebhookController::class, 'handleWebhook');
```


#### Let's set our controller
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use PrevailExcel\MarasoftPay\Facades\MarasoftPay;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Marasoft Pay Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        try{
            return MarasoftPay::getLink()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=> $e->getMessage(), 'type'=>'error']);
        }        
    }

    /**
     * Obtain Marasoft Pay payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = marasoftpay()->getPaymentData();

        dd($paymentDetails);
        // Now you have the payment details,
        // you can store the reference ID in your db.
        // you can then redirect or do whatever you want
    }
}
```

```php
/**
 *  In the case where you need to pass the data from your
 *  controller or via your client or app instead of a form
 *  
 */
 $data = [       
        'name' => "Prevail Ambrose",
        'email_address' => "example@gmail.com",
        'phone_number' => "08100000000",
        'amount' => "9000",
        'description' => "Gold Color"
    ];

    // if monolithic, do
    return MarasoftPay::getLink($data)->redirectNow();

    // if API, do
    return MarasoftPay::getLink($data, true);

```

### Lets pay with bank transfer now.
Here you can get a dynamnic account `payWithBankTransfer($amount)` for a one time payment , or you can get a reserved account `getReservedAccount($data)` or recurring payments.
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PrevailExcel\MarasoftPay\Facades\MarasoftPay;

class PaymentController extends Controller
{  
    /**
     * You collect data from your blade form
     * and this returns the Account details for payment
     */
    public function createPayment()
    {
        try {
            // You can use the global helper marasoftpay()->method() or the Facade MarasoftPay::method().
            
            // Dynamic Account Payment
            return MarasoftPay::payWithBankTransfer("100000");

            // Reserved Account Payment
            $data = [        
                'first_name' => "Prevail",
                'last_name' => "Ambrose",
                'phone_number' => "08052344545",
                'tag' => "whatever",
                'bvn' => "2522222222"
            ];
            return MarasoftPay::getReservedAccount($data);
            
        } catch (\Exception $e) {
            return redirect()->back()->withMessage(['msg' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function handleWebhook()
    {
        // verify webhook and get data
        marasoftpay()->getWebhookData()->proccessData(function ($data) {
            // Do something with $data
            logger($data);
            // If you have heavy operations, dispatch your queued jobs for them here
            // OrderJob::dispatch($data);
        });
        
        // Acknowledge you received the response
        return http_response_code(200);
    }
}
```
### Handling Webhook

This will return data that includes the account details which you will display or send to your user to make payment.
You can listen to the webhook and service the user. Write the heavy operations inside the `handleWebhook()` method.

> This package recommends to use a queued job to proccess the webhook data especially if you handle heavy operations like sending mail and more 

##### How does the webhook routing `Route::webhook(Controller::class, 'methodName')` work?

Behind the scenes, by default this will register a POST route `'marasoftpay/webhook'` to the controller and method you provide. Because the app that sends webhooks to you has no way of getting a csrf-token, you must add that route to the except array of the VerifyCsrfToken middleware:
```php
protected $except = [
    'marasoftpay/webhook',
];
```


#### A sample form will look like so:
```blade
<form method="POST" action="{{ route('pay') }}">
    @csrf
    <div class="form-group" style="margin-bottom: 10px;">
        <label for="name">Name</label>
        <input class="form-control" type="text" name="name" />
    </div>
    <div class="form-group" style="margin-bottom: 10px;">
        <label for="phone-number">Phone Number</label>
        <input class="form-control" type="tel" name="phone" required />
    </div>
    <div class="form-group" style="margin-bottom: 10px;">
        <label for="email">Email</label>
        <input class="form-control" type="email" name="email" required />
    </div>
    <div class="form-group" style="margin-bottom: 10px;">
        <label for="amount">Amount</label>
        <input class="form-control" type="number" name="amount" required />
    </div>
    <input value="This is the description" type="hidden" name="description" />    
    <div class="form-submit">
        <button class="btn btn-primary btn-block" type="submit"> Pay </button>
    </div>
</form>
```
When clicking the submit button the customer gets redirected to the Payment page.

So now the customer did some actions there (hopefully he or she paid the order) and now the package will redirect the customer to the Callback URL `Route::callback()`.

We must validate if the redirect to our site is a valid request (we don't want imposters to wrongfully place non-paid order).

In the controller that handles the request coming from the payment provider, we have

`MarasoftPay::getPaymentData()` - This function calls the `verifyTransaction()` methods and ensure it is a valid transaction else it throws an exception.








## Todo

* Add Comprehensive Tests

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## How can I thank you?

Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter or HackerNews? Spread the word!

Thanks!
Chimeremeze Prevail Ejimadu, Akindipe Ambrose.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
