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

trait Account
{
    /**
     * Check your balance for the different currencies available
     * 
     * @return array
     */
    public function checkBalance(): array
    {
        return $this->setHttpResponse('/checkbalance', 'POST')->getResponse();
    }

    /**
     * Generate account statements with custom date ranges
     * 
     * @param null|string $start_date  Start Date in format d-m-y
     * @param null|string $end_date End Date in format d-m-y
     * @return array
     */
    public function accountStatement(?string $start_date = null, ?string $end_date = null): array
    {

        if (!$start_date)
            $start_date = request()->start_date ?? null;
        if (!$end_date)
            $end_date = request()->$end_date ?? null;

        $data = array_filter([
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
        return $this->setHttpResponse('/account_history/account_statement', 'POST', $data)->getResponse();
    }
    
    /**
     * Generate transfer history statements with custom date ranges
     * 
     * @param null|string $start_date  Start Date in format d-m-y
     * @param null|string $end_date End Date in format d-m-y
     * @return array
     */
    public function transferHistory(?string $start_date = null, ?string $end_date = null): array
    {

        if (!$start_date)
            $start_date = request()->start_date ?? null;
        if (!$end_date)
            $end_date = request()->$end_date ?? null;

        $data = array_filter([
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
        return $this->setHttpResponse('/account_history/transfers', 'POST', $data)->getResponse();
    }
    
    /**
     * Generate payments history statements with custom date ranges
     * 
     * @param null|string $start_date  Start Date in format d-m-y
     * @param null|string $end_date End Date in format d-m-y
     * @return array
     */
    public function paymentsHistory(?string $start_date = null, ?string $end_date = null): array
    {

        if (!$start_date)
            $start_date = request()->start_date ?? null;
        if (!$end_date)
            $end_date = request()->$end_date ?? null;

        $data = array_filter([
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
        return $this->setHttpResponse('/account_history/transactions', 'POST', $data)->getResponse();
    }
    
    /**
     * Generate reserved account history statements with custom date ranges
     * 
     * @param null|string $start_date  Start Date in format d-m-y
     * @param null|string $end_date End Date in format d-m-y
     * @return array
     */
    public function reservedAccountHistory(?string $start_date = null, ?string $end_date = null): array
    {

        if (!$start_date)
            $start_date = request()->start_date ?? null;
        if (!$end_date)
            $end_date = request()->$end_date ?? null;

        $data = array_filter([
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
        return $this->setHttpResponse('/account_history/reserved_account', 'POST', $data)->getResponse();
    }
}
