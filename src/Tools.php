<?php

namespace PrevailExcel\MarasoftPay;

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

trait Tools
{
    
    /**
     * Get all the bank codes for all existing banks in our operating countries. 
     * 
     * @param null|string $amount
     * @return array
     */
    public function getBanks(?string $currency = null): array
    {
        if(!$currency)
        $currency = request()->currency ?? "NGN";

        $data = array_filter([
            "currency" => $currency
        ]);

        return $this->setHttpResponse('/getbanks', 'POST', $data)->getResponse();
    }
    
    /**
     * Verify the status of a transaction carried out on your Marasoft Pay account  
     * 
     * @param null|string $ref
     * @return array
     */
    public function verifyTransaction(?string $ref = null): array
    {
        if(!$ref)
        $ref = request()->ref;

        $data = array_filter([
            "transaction_ref" => $ref
        ]);

        return $this->setHttpResponse('/checktransaction', 'POST', $data)->getResponse();
    }
    
    /**
     * Verify the status of a transfer carried out from your Marasoft Pay account  
     * 
     * @param null|string $ref
     * @return array
     */
    public function verifyTransfer(?string $ref = null): array
    {
        if(!$ref)
        $ref = request()->ref;

        $data = array_filter([
            "transaction_ref" => $ref
        ]);

        return $this->setHttpResponse('/checktransfer', 'POST', $data)->getResponse();
    }
    
    /**
     * Verify the owner of a bank account using the bank code and the account uumber 
     * 
     * @param null|string $bank_code  Bank code from getbanks endpoint
     * @param null|string $account_number Users account number
     * @return array
     */
    public function confirmAccount(?string $bank_code = null, ?string $account_number = null): array
    {

        if (!$bank_code)
            $bank_code = request()->bank_code ?? null;
        if (!$account_number)
            $account_number = request()->$account_number ?? null;

        $data = array_filter([
            "bank_code" => $bank_code,
            "account_number" => $account_number
        ]);
        return $this->setHttpResponse('/resolvebank', 'POST', $data)->getResponse();
    }

}
