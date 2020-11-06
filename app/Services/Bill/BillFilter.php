<?php

namespace App\Services\Bill;

use App\Models\BillingStatus;
use Carbon\CarbonInterval;

class BillFilter
{
    /**
     * @var string
     */
    private $billStatusFilter;
    private $timeFilter;
    private $time_bill;

    /**
     * BillFilter constructor.
     *
     * @param string $billStatusFilter
     * @param string $timeFilter
     * @param string $time_bill
     */
    public function __construct(string $billStatusFilter, string $timeFilter, string $time_bill)
    {
        $this->billStatusFilter = $billStatusFilter;
        $this->timeFilter = $timeFilter;
        $this->time_bill = $time_bill;
    }

    /**
     * @param $record
     *
     * @return bool
     * @throws \Exception
     */
    public function filterByTime($record)
    {
        if (!($record->{$this->time_bill} instanceof CarbonInterval)){
            throw new \Exception('Time Bill must be the instanceof CarbonInterval');
        }

        $commonCondition = $record->time_used->months === 0 && $record->time_used->dayz === 0 && $record->time_used->hours === 0;

        switch ($this->timeFilter) {
            case 'No Time Under 2 Min':
                return !($commonCondition && $record->time_used->minutes < 2);
                break;
            case 'No Time Under 5 Min':
                return !($commonCondition && $record->time_used->minutes < 5);
                break;
            case 'No Time Under 15 Min':
                return !($commonCondition && $record->time_used->minutes < 15);
                break;
            case 'No Time Under 30 Min':
                return !($commonCondition && $record->time_used->minutes < 30);
                break;
            case 'No Time Under 60 Min':
                return !($commonCondition && $record->time_used->minutes < 60);
                break;
            default:
                return true;
        }
    }

    /**
     * @param $record
     *
     * @return bool
     */
    public function filterByStatus($record)
    {
        switch ($this->billStatusFilter){
            case 'Billed Time':
                return $record->billing_status_id === BillingStatus::INITIAL_STATUSES['Billed']['id'];
                break;
            case 'Not Billed Time (With Parked Time)':
                return ($record->billing_status_id !== BillingStatus::INITIAL_STATUSES['Billed']['id']);
                break;
            case 'Not Billed Time (Without Parked Time)':
                return ($record->billing_status_id !== BillingStatus::INITIAL_STATUSES['Billed']['id'] &&
                        $record->billing_status_id !== BillingStatus::INITIAL_STATUSES['Parked']['id']);
                break;
            case 'Parked Time':
                return ($record->billing_status_id === BillingStatus::INITIAL_STATUSES['Parked']['id']);
                break;
            case 'Not Billable':
                return ($record->billing_status_id === BillingStatus::INITIAL_STATUSES['Unknown']['id']);
                break;
            default:
                return true;
        }
    }
}