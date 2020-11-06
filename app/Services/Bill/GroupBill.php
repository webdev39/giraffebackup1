<?php

namespace App\Services\Bill;

use App\Models\BillingStatus;
use App\Services\Reports\GroupReportsDetail;

class GroupBill extends GroupReportsDetail
{
    private $billStatuses = [];
    private $filter;

    /**
     * GroupBill constructor.
     *
     * @param $groupBy
     * @param $time_used
     * @param $time_bill
     */
    public function __construct($groupBy, $time_used, $time_bill)
    {
        parent::__construct($groupBy, $time_used, $time_bill);

        foreach (BillingStatus::INITIAL_STATUSES as $billing_statuses) {
            if ($billing_statuses['id'] === BillingStatus::INITIAL_STATUSES['Unknown']['id']){
                $this->billStatuses[$billing_statuses['id']] = snake_case($billing_statuses['name']);
                continue;
            }

            $this->billStatuses[$billing_statuses['id']] = snake_case($billing_statuses['name']) . '_time';
        }

        $this->billStatuses[5] = 'all_time';
    }

    /**
     * @param BillFilter $filter
     */
    public function setFilter(BillFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param $record
     */
    protected function noneGroupBy($record)
    {
        if ($this->filter->filterByTime($record) && $this->filter->filterByStatus($record)){
            $this->records[] = $record;
        }
    }
}
