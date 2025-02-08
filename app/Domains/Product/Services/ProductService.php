<?php

namespace App\Domains\Product\Services;

use App\Domains\Product\DTOs\ProductDTO;
use App\Domains\Product\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    )
    {}
    
    public function create(ProductDTO $dto, ?UploadedFile $photo = null)
    {
        $data = $dto->toArray();

        if ($photo) {
            $data['photo'] = $photo->store('produtos', 'public');
        }

        return $this->productRepository->create($data)->toArray();
    }   
    
    public function findById(int $id)
    {
        return $this->productRepository->findById($id)->toArray();
    }
    
    public function update(int $id, ProductDTO $dto, ?UploadedFile $photo = null): bool
    {
        $data = $dto->toArray();

        if ($photo) {
            $this->deletePhoto($id);

            $data['photo'] = $photo->store('produtos', 'public');
        }

        return $this->productRepository->update($id, $data);
    }
    
    public function delete(int $id): void
    {
        $this->deletePhoto($id);

        $this->productRepository->delete($id);
    }

    public function findAll(array $filters): array
    {
        return $this->productRepository->findAll($filters)->toArray();
    }

    private function deletePhoto(int $id): void
    {
        $product = $this->findById($id);

        if ($product['photo']) {
            Storage::disk('public')->delete($product['photo']);
        }
    }
}
