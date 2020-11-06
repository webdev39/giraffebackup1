<?php

namespace App\Http\Resources;

use App\Models\BillLayout;
use App\Models\BillTimer;
use App\Models\TimerBilling;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

/**
 * Class BillDetailsResource
 *
 * @package App\Http\Resources
 *
 * @property integer                $id
 * @property float                  $time
 * @property float                  $rate
 * @property float                  $amount
 * @property float                  $status
 * @property User                   $customer
 * @property BillLayout             $billLayout
 * @property BillTimer              $billTimers
 * @property TimerBilling           $timerBillings
 */
class BillDetailsResource extends Resource
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
            'time'              => round($this->time, 2),
            'rate'              => round($this->rate, 2),
            'amount'            => round($this->amount, 2),
            'customer_name'     => $this->customer->name,
            'customer'          => $this->customer,
            'invoice_number'    => $this->invoice_number,
            'status'            => $this->status,
            'bill_date'         => $this->billLayout->bill_date,
            'bill_layout'       => $this->billLayout,
            'bill_timers'       => $this->billTimers,
            'timer_billings'    => $this->timerBillings,
            'logs'              => $this->logs,
            'user'              => Auth::user(),
        ];
    }
}
