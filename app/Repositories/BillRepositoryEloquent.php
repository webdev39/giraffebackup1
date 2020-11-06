<?php

namespace App\Repositories;

use App\Models\Bill;

class BillRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * @return string
     */
    public function model()
    {
        return Bill::class;
    }

    /**
     * @param array $attributes
     * @param null  $billId
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function createOrUpdate(array $attributes, $billId = null)
    {
        if ($billId) {
            if ($bill = $this->model->find($billId)) {
                return $bill;
            }
        }

        $bill = $this->model->fill($attributes);

        if ($billId) {
            $bill->id = $billId;
        }

        $bill->save();

        return $bill;
    }

    /**
     * @param array $attributes
     * @param null  $billId
     *
     * @return mixed
     */
    public function createOrUpdateBillLayout(array $attributes, $billId = null)
    {
        $bill = $this->model->find($billId);

        if ($billLayout = $bill->billLayout) {
            return $billLayout->update($attributes);
        }

        return $bill->billLayout()->create(array_merge([
            'bill_date' => now()->toDateTimeString(),
        ], $attributes));
    }

}