<?php

namespace PrevailExcel\MarasoftPay;

use Nette\Utils\Random;

/*
 * This file is part of the Laravel MarasoftPay package.
 *
 * (c) Prevail Ejimadu <prevailexcellent@gmail.com>, Akindipe Ambrose <akindipe.abiola13@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

trait Payout
{

    /**
     * Move funds from your Marasoft Pay balance to a bank account. 
     * 
     * @param array $data
     * @return array
     */
    public function transfer($data = null): array
    {

        $def = [
            "transaction_ref" => Random::generate(),
            "currency" => request()->currency ?? "NGN",
        ];

        if ($data == null) {
            $data = [
                'bank_code' => request()->bank_code,
                'account_number' => request()->account_number,
                'amount' => request()->amount,
                'description' => request()->description,
            ];
        }
        $data = array_merge($def, $data);

        return $this->setHttpResponse('/createtransfer', 'POST', array_filter($data))->getResponse();
    }
}
