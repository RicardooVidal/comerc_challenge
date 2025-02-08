<?php

namespace App\Http\Controllers\Order;

use App\Domains\Order\DTOs\OrderDTO;
use App\Domains\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AllOrderRequest;
use App\Http\Requests\Order\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    )
    {}

    public function index(AllOrderRequest $request): JsonResponse
    {
        $orders = $this->orderService->findAll($request->all());
        
        return response()->json($orders);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $dto = OrderDTO::fromArray($request->validated());
        $order = $this->orderService->create($dto);

        return response()->json($order, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        return response()->json($order);
    }

    public function update(OrderRequest $request, $id): JsonResponse
    {
        $dto = OrderDTO::fromArray($request->validated());
        $order = $this->orderService->update($id, $dto);

        return response()->json($order);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->orderService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
