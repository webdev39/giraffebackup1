<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ArchiveCustomerRequest;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\UserTenant;
use App\Repositories\CustomerRepositoryEloquent;
use App\Services\Customer\CustomerService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Exceptions\ValidatorException;

class CustomerController extends Controller
{
    /** @var CustomerRepositoryEloquent */
    public $customerRepo;

    /** @var CustomerService */
    public $customerService;

    /**
     * CustomerController constructor.
     */
    public function __construct()
    {
        $this->customerService  = app('CustomerSer');
        $this->customerRepo     = app('CustomerRepo');
    }

    /**
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $customers  = $this->customerService->getCustomersByUserTenant($userTenant, request('status'));

        return response()->json([
            'clients' => CustomerResource::collection($customers)
        ]);
    }

    /**
     * @param int $customerId
     *
     * @return JsonResponse
     */
    public function show(int $customerId)
    {
        Auth::failIfHasNoPermission(Permission::MANAGE_CUSTOMERS_PERMISSION);

        $customer = $this->customerService->getCustomerById($customerId);

        if (!$customer) {
            abort(404, 'Client is not found');
        }

        if (!Auth::user()->can('update', $customer)) {
            abort(403, 'User has no permissions to update this client');
        }

        return response()->json([
            'client' => new CustomerResource($customer)
        ]);
    }

    /**
     * @param CreateCustomerRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function create(CreateCustomerRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        Auth::failIfHasNoPermission(Permission::MANAGE_CUSTOMERS_PERMISSION);

        $customer = $this->customerService->createCustomer($request->all(), $userTenant->tenant_id);
        $customer = $this->customerService->getCustomerById($customer->id);

        return response()->json([
            'client' => new CustomerResource($customer)
        ]);
    }

    /**
     * @param UpdateCustomerRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function update(UpdateCustomerRequest $request)
    {
        $customer = $this->customerService->getCustomerById($request->get('id'));

        Auth::user()->can('update', $customer);

        $customer = $this->customerService->updateCustomer($request->all(), $customer->id);
        $customer = $this->customerService->getCustomerById($customer->id);

        return response()->json([
            'client' => new CustomerResource($customer)
        ]);
    }

    /**
     * @param ArchiveCustomerRequest $request
     * @return JsonResponse
     *
     * @throws ValidatorException
     */
    public function archive(ArchiveCustomerRequest $request)
    {
        $customer = $this->customerService->getCustomerById($request->get('id'));

        Auth::user()->can('update', $customer);

        $customer = $this->customerService->archiveCustomer($customer->id);
        $customer = $this->customerService->getCustomerById($customer->id);

        return response()->json([
            'client' => new CustomerResource($customer)
        ]);
    }

    /**
     * @param ArchiveCustomerRequest $request
     * @return JsonResponse
     *
     * @throws ValidatorException
     */
    public function restore(ArchiveCustomerRequest $request)
    {
        $customer = $this->customerService->getCustomerById($request->get('id'));

        Auth::user()->can('update', $customer);

        $customer = $this->customerService->restoreCustomer($customer->id);
        $customer = $this->customerService->getCustomerById($customer->id);

        return response()->json([
            'client' => new CustomerResource($customer)
        ]);
    }

    /**
     * @param int $customerId
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function destroy(int $customerId)
    {
        /** @var Customer $customer */
        $customer = $this->customerService->getCustomerById($customerId);

        Auth::user()->can('update', $customer);

        $this->customerService->destroyCustomer($customerId);

        return response()->json([
            'success' => true
        ]);
    }
}
