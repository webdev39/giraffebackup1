<?php

namespace App\Http\Resources;

use App\Models\BillLayout;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class BillResource
 *
 * @package App\Http\Resources
 *
 * @property integer                $id
 * @property integer                $invoice_number
 * @property float                  $time
 * @property float                  $rate
 * @property float                  $amount
 * @property User                   $customer
 * @property BillLayout             $billLayout
 */
class BillResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource)) {
            return null;
        }

        return [
            'id'                => $this->id,
            'invoice_number'    => $this->invoice_number,
            'time'              => round($this->time, 2),
            'rate'              => round($this->rate, 2),
            'amount'            => round($this->amount, 2),
            'customer_name'     => $this->customer->name,
            'bill_date'         => optional($this->billLayout)->bill_date,
        ];
    }
}
