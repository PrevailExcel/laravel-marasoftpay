<?php

namespace PrevailExcel\MarasoftPay;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Nette\Utils\Random;

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MarasoftPay
{
    use Collection, Account, Payout, Tools;


    /**
     * Issue encryption Key from your MarasoftPay Dashboard
     * @var string
     */
    protected $encryptionKey;

    /**
     * Issue Public Key from your MarasoftPay Dashboard
     * @var string
     */
    protected $publicKey;

    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     *  Response from requests made to MarasoftPay
     * @var mixed
     */
    protected $response;

    /**
     * MarasoftPay API base Url
     * @var string
     */
    protected $baseUrl;

    /**
     * Generated url for user
     * @var string
     */
    protected $url;

    /**
     * MarasoftPay API Enviroment
     * @var string
     */
    protected $env;

    /**
     * Secret Hash set up on MarasoftPay Dashboard
     * @var string
     */
    protected $hash;

    /**
     * Verified Data from Webhook
     */
    protected $webhookData;

    /**
     * Your callback Url. You can set this in your .env or you can add 
     * it to the $data in the methods that require that option.
     * @var string
     */
    protected $callbackUrl;

    public function __construct()
    {
        $this->setUp();
        $this->setRequestOptions();
    }

    /**
     * Set properties from MarasoftPay config file
     */
    private function setUp()
    {
        $this->publicKey = Config::get('marasoftpay.publicKey');
        $this->encryptionKey = Config::get('marasoftpay.encryptionKey');
        $this->env = Config::get('marasoftpay.env');
        $this->hash = Config::get('marasoftpay.hash');
        $this->baseUrl = Config::get('marasoftpay.baseUrl');
    }

    /**
     * Set options for making the Client request
     */
    private function setRequestOptions()
    {
        $headers = [
            "sdk-version" => "prevailexcel/laravel-marasoftpay~1.0.0",
            'Content-Type' => 'multipart/form-data,application/json,application/x-www-form-urlencoded',
            'Accept'        => 'application/json'
        ];
        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => $headers
            ]
        );
    }

    /**
     * @param string $relativeUrl
     * @param string $method
     * @param array $body
     * @return MarasoftPay
     * @throws IsNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [], bool $checkout = false)
    {
        if (is_null($method)) {
            throw new IsNullException("Empty method not allowed");
        }
        $key = [
            "enc_key" => $this->encryptionKey,
            'request_type' => $this->env
        ];
        $body = array_merge($key, $body);

        if ($checkout) {
            $this->baseUrl = "https://checkout.marasoftpay.live";
            $options = ["body" => json_encode(["data" => $body])];
        } else {

            $multipartData = [];

            // Loop through the body and create multipart data
            foreach ($body as $name => $contents) {
                $multipartData[] = [
                    'name'     => $name,
                    'contents' => $contents,
                ];
            }
            $options = ["multipart" => $multipartData];
        }

        $this->response = $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            $options
        );

        return $this;
    }

    /**
     * Get the whole response from a get operation
     * @return array
     */
    private function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Verify webhook data
     * 
     * @return MarasoftPay
     * @throws IsNullException
     */
    public function getWebhookData()
    {
        if (request()->header('Verification-Hash'))
            $verified = $this->hash == request()->header('Verification-Hash');

        if ($verified) {

            $data = json_decode(request()->getContent(), true);

            if (isset($data['channel'])) {
                $channel = $data['channel'];
                if ($channel == "CHECKOUT_COLLECTIONS")
                    //check if it's Collection or Reserved
                    $data['event'] = 'CHECKOUT';
                else
                    $data['event'] = 'RESERVED';
            } else {
                $data['event'] = 'PAYOUT';
            }

            $this->webhookData = json_encode($data);
            return $this;
        } else
            throw IsNullException::make();
    }

    /**
     * Handle webhook data
     * @return array
     */
    public function proccessData(callable|Closure $callback)
    {
        call_user_func($callback, $this->webhookData);
        return true;
    }

    /**
     * Creates a unique link that directs customers to the checkout page for completing their payments.
     * @param array|null $data
     * @param bool $show
     * @return MarasoftPay|array
     */
    public function getLink($data = null, bool $show = false)
    {

        $def = [
            'public_key' => $this->publicKey,
            'merchant_tx_ref' => Random::generate(),
            'currency' => 'NGN',
            'user_bear_charge' => 'no',
            'preferred_payment_option' => 'bank',
            'redirect_url' => route('marasoftpay.lara.callback'),
        ];

        if ($data == null) {
            $data = [
                'name' => request()->name,
                'email_address' => request()->email,
                'phone_number' => request()->phone,
                'amount' => request()->amount,
                'description' => request()->description ?? []
            ];
        }
        $data = array_merge($def, $data);

        $response = $this->setHttpResponse('/initiate_transaction', 'POST', array_filter($data), true)->getResponse();
        if ($response["status"] == "success") {
            $this->url = $response["url"];

            // If $show is true, return the response, else return the class  
            if ($show)
                return $response;
            return $this;
        }
        return $response;
    }


    /**
     * Get payment data
     * @return array
     */
    public function getPaymentData()
    {
        $ref = request()->txn_ref;
        $paymentdata = $this->verifyTransaction($ref);
        return $paymentdata;
    }
}
