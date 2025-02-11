<?php

namespace App\Http\Controllers\Customer;

use App\Domains\Customer\DTOs\CustomerDTO;
use App\Domains\Customer\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AllCustomerRequest;
use App\Http\Requests\Customer\CustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function index(AllCustomerRequest $request): JsonResponse
    {
        $customers = $this->customerService->findAll($request->all());

        return response()->json($customers);
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $dto = CustomerDTO::fromArray($request->validated());
        $customer = $this->customerService->create($dto);

        return response()->json($customer, Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        return response()->json($customer);
    }

    public function update(CustomerRequest $request, $id): JsonResponse
    {
        $dto = CustomerDTO::fromArray($request->validated());
        $customer = $this->customerService->update($id, $dto);

        return response()->json($customer);
    }

    public function destroy($id): JsonResponse
    {
        $this->customerService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
