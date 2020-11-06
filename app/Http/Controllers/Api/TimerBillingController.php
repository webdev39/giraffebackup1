<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MassUpdateBillingStatusRequest;
use App\Http\Requests\UpdateBillingStatusRequest;
use App\Models\BillingStatus;
use App\Models\Permission;
use App\Services\Billing\BillingService;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimerBillingController extends Controller
{
    /** @var BillingService */
    private $billingService;

    /**
     * TimerBillingController constructor.
     */
    public function __construct()
    {
        $this->billingService = app('BillingSer');
    }

    /**
     * @param null $year
     *
     * @return JsonResponse
     */
    public function getYearOverview($year = null)
    {
        $validator = Validator::make(
            ['year' => $year],
            ['id'   => "nullable|date_format:Y"]
        );

        if ($validator->failed()) {
            abort(403, 'Invalid date');
        }

        if (!Auth::user()->can('readBillingReports', Auth::userTenant())) {
            abort(403, 'Sorry you are not Authorized');
        }

        $records = $this->billingService->getBillingYearOverview(Auth::userTenant(), $year);

        return response()->json([
            'message' => 'Year overview feed',
            'records' => $records
        ]);
    }









    /**
     * @param UpdateBillingStatusRequest $billingStatusRequest
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateBillingStatusRequest $billingStatusRequest)
    {
        Auth::failIfHasNoPermission(Permission::READ_BILLING_PERMISSION);

        $billingStatus = app('BillingSer')->changeTimerBillingStatus(
            $billingStatusRequest->input('timerBillingId'),
            $billingStatusRequest->input('billingStatusId')
        );

        if ($billingStatus instanceof \Exception){
            return response()->json(['message' => $billingStatus->getMessage()], 400);
        }

        return response()->json(['message' => 'Billing status changed successfully', 'timerBilling' => $billingStatus ]);
    }

    /**
     * @param MassUpdateBillingStatusRequest $massUpdateBillingStatusRequest
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function massStatusUpdate(MassUpdateBillingStatusRequest $massUpdateBillingStatusRequest)
    {
        Auth::failIfHasNoPermission(Permission::READ_BILLING_PERMISSION);

        app('BillingSer')->massChangeTimerBillingStatus(
            $massUpdateBillingStatusRequest->input('timerBillingIds'),
            $massUpdateBillingStatusRequest->input('billingStatusId')
        );

        return response()->json(['message' => 'Billing statuses changed successfully'], 200);
    }

}