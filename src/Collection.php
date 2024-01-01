<?php

namespace PrevailExcel\MarasoftPay;

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

trait Collection
{

    /**
     * Fluent method to redirect to MarasoftPay Payment Page
     */
    public function redirectNow()
    {
        return redirect($this->url);
    }

    /**
     * Get temporary accounts that are used for one time payments.
     * The accounts are tied to a specific amount to be paid and an account name.
     * @param string|null $amount
     * @return array
     */
    public function payWithBankTransfer(?string $amount = null): array
    {
        if(!$amount)
        $amount = request()->amount;

        $data = array_filter([
            "amount" => $amount,
            "transaction_ref" => Random::generate()
        ]);

        return $this->setHttpResponse('/generate_dynamic_account', 'POST', $data)->getResponse();
    }

    /**
     * Reserved accounts are accounts that remains valid once they are generated.
     * Multiple payments can be made to this account with no limit to the number of transactions.
     * 
     * @param array $data
     * @return array
     */
    public function getReservedAccount($data = null): array
    {
        
        if ($data == null) {
            $data = [
                'first_name' => request()->first_name,
                'last_name' => request()->last_name,
                'phone_number' => request()->phone_number,
                'tag' => request()->tag,
                'bvn' => request()->bvn,
            ];
        }

        return $this->setHttpResponse('/reserved_account/create', 'POST', array_filter($data))->getResponse();
    }    

    /**
     * Make payment with MPESA mobile money
     * 
     * @param array $data
     * @return array
     */
    public function payWithMobileMoney($data = null): array
    {
        
        $def = [
            "transaction_ref" => Random::generate()
        ];

        $data = array_filter([
            'amount' => request()->amount,            
            'phone' => request()->phone,
            'description' => request()->description ?? [],
        ]);
        
        $data = array_merge($def, $data);

        return $this->setHttpResponse('/mobile_money/initiate_payment', 'POST', array_filter($data))->getResponse();
    }

    /**
     * Make payment with MPESA mobile money
     * 
     * @param array $data
     * @return array
     */
    public function ussd($data = null): array
    {
        $def = [
            "merchant_ref" => Random::generate(),
            "ref_id" => Random::generate(),
            "currency" => request()->currency ?? "NGN",
            "user_bear_charge" => Config::get('marasoftpay.user_bear_charge'),
            'redirect_url' => route('marasoftpay.lara.callback'),
        ];

        if ($data == null) {
            $data = [
                'user_bank_code' => request()->user_bank_code,
                'name' => request()->name,
                'email_address' => request()->email_address,
                'phone_number' => request()->phone_number,
                'amount' => request()->amount,
                'description' => request()->description,
            ];
        }
        $data = array_merge($def, $data);

        return $this->setHttpResponse('/ussd/get_ussd_code', 'POST', array_filter($data))->getResponse();
    }
}
