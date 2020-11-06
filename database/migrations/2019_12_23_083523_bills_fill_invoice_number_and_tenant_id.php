<?php

use App\Models\Bill;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BillsFillInvoiceNumberAndTenantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Bill::with('customer')->get()->each(function(Bill $bill) {
            $bill->invoice_number = $bill->id;
            $bill->tenant_id = optional($bill->customer)->tenant_id;
            $bill->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
