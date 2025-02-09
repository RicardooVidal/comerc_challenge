<?php

namespace App\Domains\Product\Services;

use App\Domains\Product\DTOs\ProductTypeDTO;
use App\Domains\Product\Repositories\ProductTypeRepository;

class ProductTypeService
{
    public function __construct(
        private readonly ProductTypeRepository $productTypeRepository
    )
    {}
    
    public function create(ProductTypeDTO $dto)
    {
        $data = $dto->toArray();

        return $this->productTypeRepository->create($data)->toArray();
    }   
    
    public function findById(int $id)
    {
        return $this->productTypeRepository->findById($id)->toArray();
    }
    
    public function update(int $id, ProductTypeDTO $dto): bool
    {
        $data = $dto->toArray();
    
        return $this->productTypeRepository->update($id, $data);
    }
    
    public function delete(int $id): void
    {
        $this->productTypeRepository->delete($id);
    }

    public function findAll(array $filters): array
    {
        return $this->productTypeRepository->findAll($filters)->toArray();
    }
}
