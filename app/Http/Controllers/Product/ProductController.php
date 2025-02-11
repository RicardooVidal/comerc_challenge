<?php

namespace App\Http\Controllers\Product;

use App\Domains\Product\DTOs\ProductDTO;
use App\Domains\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AllProductRequest;
use App\Http\Requests\Product\ProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(AllProductRequest $request): JsonResponse
    {
        $products = $this->productService->findAll($request->all());

        return response()->json($products);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
        }

        $dto = ProductDTO::fromArray($data);
        $product = $this->productService->create($dto, $photo);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return response()->json($product);
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
        }

        $dto = ProductDTO::fromArray($data);
        $product = $this->productService->update($id, $dto, $photo);

        return response()->json($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
