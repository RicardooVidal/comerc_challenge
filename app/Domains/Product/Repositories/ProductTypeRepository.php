<?php

namespace App\Domains\Product\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Product\Entities\ProductType;
use Illuminate\Support\Collection;

class ProductTypeRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly ProductType $productType
    ) {
    }

    public function findById(int $id): ?ProductType
    {
        return $this->productType->findOrFail($id);
    }

    public function findAll(array $data): Collection
    {
        return $this->productType->where($data)->get();
    }

    public function create(array $data): ProductType
    {
        return $this->productType->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->productType->findOrFail($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->productType->findOrFail($id)->delete();
    }
}
