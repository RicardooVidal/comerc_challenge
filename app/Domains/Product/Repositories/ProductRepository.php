<?php

namespace App\Domains\Product\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Product\Entities\Product;
use Illuminate\Support\Collection;

class ProductRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly Product $product
    )
    {}

    public function findById(int $id): ?Product
    {
        return $this->product->findOrFail($id);
    }

    public function findAll(array $data): Collection
    {
        return $this->product->where($data)->get();
    }

    public function create(array $data): Product
    {
        return $this->product->create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        return $this->product->findOrFail($id)->update($data);
    }
    
    public function delete(int $id): bool
    {
        return $this->product->findOrFail($id)->delete();
    }
}
