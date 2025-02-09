<?php

namespace App\Domains\Product\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Product\Entities\ProductType;

class ProductTypeRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly ProductType $productType
    )
    {}

    public function create(array $data)
    {
        return $this->productType->create($data);
    }
    
    public function findById(int $id)
    {
        return $this->productType->findOrFail($id);
    }
    
    public function update(int $id, array $data)
    {
        return $this->productType->findOrFail($id)->update($data);
    }
    
    public function delete(int $id)
    {
        return $this->productType->findOrFail($id)->delete();
    }
    
    public function findAll(array $data)
    {
        return $this->productType->where($data)->get();
    }
}
