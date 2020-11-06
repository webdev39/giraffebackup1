<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 09.03.18
 * Time: 11:06
 */

namespace App\Repositories;

use App\Models\BillingStatus;

class BillingStatusRepositoryEloquent extends BaseRepositoryEloquent
{
    public function model()
    {
        return BillingStatus::class;
    }
}