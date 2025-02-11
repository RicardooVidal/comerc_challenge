<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\DTOs\ProductTypeDTO;
use App\Domains\Product\Services\ProductTypeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AllProductTypeRequest;
use App\Http\Requests\Product\ProductTypeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductTypeController extends Controller
{
    public function __construct(
        private readonly ProductTypeService $productTypeService
    ) {
    }

    public function index(AllProductTypeRequest $request): JsonResponse
    {
        $products = $this->productTypeService->findAll($request->all());

        return response()->json($products);
    }

    public function store(ProductTypeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
        }

        $dto = ProductTypeDTO::fromArray($data);
        $productType = $this->productTypeService->create($dto, $photo);

        return response()->json($productType, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productTypeService->findById($id);

        return response()->json($product);
    }

    public function update(ProductTypeRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
        }

        $dto = ProductTypeDTO::fromArray($data);
        $productType = $this->productTypeService->update($id, $dto, $photo);

        return response()->json($productType);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productTypeService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
