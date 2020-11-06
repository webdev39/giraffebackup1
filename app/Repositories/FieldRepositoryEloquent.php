<?php

namespace App\Repositories;

use App\Models\BillingStatus;
use App\Models\BillLayoutType;
use App\Models\Customer;
use App\Models\Field;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class FieldRepositoryEloquent
 *
 * @package App\Repositories
 * @property $model Field
 */
class FieldRepositoryEloquent extends BaseRepositoryEloquent
{
    private $selectFields = [
        'id', 'name', 'description', 'is_default'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Field::class;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFonts(): Collection
    {
        return Cache::remember('fonts', 360, function () {
            return $this->model->where('type', 'font')->get($this->selectFields);
        });
    }

    /**
     * @return Field
     */
    public function getDefaultFount(): Field
    {
        return $this->getFonts()->where('is_default', true)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getViewTypes(): Collection
    {
        return Cache::remember('view_types', 360, function () {
            return $this->model->where('type', 'view_type')->get($this->selectFields);
        });
    }

    /**
     * @return Field
     */
    public function getDefaultViewType(): Field
    {
        return $this->getViewTypes()->where('is_default', true)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBudgetTypes(): Collection
    {
        return Cache::remember('budget_types', 360, function () {
            return $this->model->where('type', 'budget_type')->get($this->selectFields);
        });
    }

    /**
     * @return Field
     */
    public function getDefaultBudgetType(): Field
    {
        return $this->getBudgetTypes()->where('is_default', true)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationTypes()
    {
        return Cache::remember('notification_types', 360, function () {
            return $this->model->where('type', 'notification_type')->get($this->selectFields);
        });
    }







    /**
     * TODO : Move to Fields
     */

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBillingStatuses()
    {
        return Cache::remember('billing_statuses', 360, function () {
            return BillingStatus::all(['id', 'name', 'alias', 'color'])
                ->whereNotIn('id', [BillingStatus::INITIAL_STATUSES['Billed']['id']]);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBillLayoutTypes()
    {
        return Cache::remember('bill_layout_types', 360, function () {
            return BillLayoutType::all(['id', 'name', 'description', 'alias']);
        });
    }

    /**
     * @return array|null
     */
    public function getCustomerStatuses()
    {
        return Cache::remember('customer_statuses', 360, function () {
            return array_flip(Customer::STATUS);
        });
    }

    /**
     * @return array|null
     */
    public function getUserStatuses()
    {
        return Cache::remember('user_statuses', 360, function () {
            return array_flip(User::STATUS);
        });
    }
}
