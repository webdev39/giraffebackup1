<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AddBillRequest;
use App\Http\Requests\BillFilterRequest;
use App\Http\Requests\BillListRequest;
use App\Http\Requests\CreateBillRequest;
use App\Http\Requests\EditBillRequest;
use App\Http\Resources\BillDetailsResource;
use App\Http\Resources\BillResource;
use App\Models\Permission;
use App\Models\UserTenant;
use App\Http\Controllers\Controller;
use App\Services\Bill\BillService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class BillController
 *
 * @package App\Http\Controllers\Api
 */
class BillController extends Controller
{
    /** @var BillService */
    private $billService;

    /**
     * BillController constructor.
     */
    public function __construct()
    {
        $this->billService = app('BillSer');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBillList(Request $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bills = $this->billService->getBillListData(
            $userTenant->tenant_id,
            [$request->get('start_month'), $request->get('end_month')],
            $request->get('client')
        );

        return response()->json([
            'bills' => BillResource::collection($bills)
        ]);
    }

    /**
     * @param BillFilterRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(BillFilterRequest $request)
    {
        if (empty($request->query())) {
            abort(400, 'Sorry your request is empty');
        }

        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bills = $this->billService->getFilteredBills($request->query());

        return response()->json([
            'message'=> 'New Bill feed',
            'records' => $bills
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBillFilterCriteria()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        $criteria           = config('bill.options');
        $defaultSelected    = config('bill.default');

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        return response()->json([
            'message'           => 'Bill filter criteria',
            'criteria'          => $criteria,
            'defaultSelected'   => $defaultSelected
        ]);
    }

    /**
     * @param CreateBillRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(CreateBillRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bill = $this->billService->createDraftBill([
            'bill_layout_type_id'   => $request->get('billLayoutTypeId', 1),
            'bill_date'             => $request->get('billDate', now()),
            'bill_timers'           => $request->get('billTimers'),
            'customer_id'           => $request->get('customerId'),
            'customer_rate'         => $request->get('rate'),
            'tenant_id'             => $userTenant->tenant_id,
            'invoice_number'        => $this->billService->getNextInvoiceNumber($userTenant->tenant_id)
        ]);

        if ($bill instanceof \Exception) {
            abort(400, 'Bill wasn\'t created');
        }

        $bill = $this->billService->getBillWithRelations($bill->id);

        return response()->json([
            'message' => 'Bill successfully created',
            'bill'    => new BillDetailsResource($bill)
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAddBill()
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bills = $this->billService->getAddBill();

        return response()->json([
            'message'   => 'Get add bills success',
            'bills'     => $bills
        ]);
    }

    /**
     * @param AddBillRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function addBill(AddBillRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bill = $this->billService->createDraftBill([
            'bill_layout_type_id'   => $request->get('billLayoutTypeId', 1),
            'bill_date'             => $request->get('billDate', now()),
            'bill_timers'           => $request->get('billTimers'),
            'customer_id'           => $request->get('customerId'),
            'customer_rate'         => $request->get('rate'),
            'tenant_id'             => $userTenant->tenant_id,
            'invoice_number'        => $this->billService->getNextInvoiceNumber($userTenant->tenant_id),
        ], $request->get('billId'));

        if ($bill instanceof \Exception){
            abort(400, 'Bill wasn\'t updated');
        }

        $bill = $this->billService->getBillWithRelations($bill->id);

        return response()->json([
            'message'   => 'Bill successfully updated',
            'bill'      => new BillDetailsResource($bill)
        ]);
    }

    /**
     * @param int $billId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getEditBill(int $billId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $bill = $this->billService->getBillWithRelations($billId);

        return response()->json([
            'message'   => 'Get bill for edit success',
            'bill'      => new BillDetailsResource($bill)
        ]);
    }

    /**
     * @param EditBillRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function editBill(EditBillRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }
        
        $bill = $this->billService->createOrUpdateBill([
            'bill_layout_type_id'   => $request->get('billLayoutTypeId', 1),
            'bill_date'             => $request->get('billDate', now()),
            'bill_timers'           => $request->get('billTimers'),
            'status'                => $request->get('billStatus'),
            'bill_log_comment'      => $request->get('billComment'),
            'customer_id'           => $request->get('customerId'),
            'customer_rate'         => $request->get('customerRate'),
            'tenant_id'             => $userTenant->tenant_id,
            'invoice_number'        => $request->get('billInvoiceNumber'),
        ], $request->get('billId'));

        if ($bill instanceof \Exception){
            dd($bill);
            abort(400, 'Failed to update bill');
        }

        $bill = $this->billService->getBillWithRelations($bill->id);

        return response()->json([
            'message'   => 'Bill successfully updated',
            'bill'      => new BillDetailsResource($bill)
        ]);
    }

    /**
     * @param $billId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function delete($billId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            return response()->json(['message'=> 'You don\'t have permission to billing'],403);
        }

        $this->billService->deleteBill($billId);

        return response()->json([
            'message' => 'Bill successfully deleted'
        ]);
    }

    /**
     * @param int $billId
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function downloadPdf(int $billId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        if (!$userTenant->can(Permission::READ_BILLING_PERMISSION['name'])) {
            abort(403, 'You don\'t have permission to billing');
        }

        $data       = app('BillPdfSer')->getBillData($billId);
        $content    = app('BillPdfSer')->getPdfContent($data);
        $filename   = app('BillPdfSer')->getPdfFilename($data);

        return response()->json([
            'content' => 'data:application/pdf;base64,'.base64_encode($content),
            'filename' => $filename,
        ]);
    }
}
