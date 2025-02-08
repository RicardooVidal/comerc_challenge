<?php

namespace App\Domains\Product\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Product\Entities\Product;

class ProductRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly Product $product
    )
    {}

    public function create(array $data)
    {
        return $this->product->create($data);
    }
    
    public function findById(int $id)
    {
        return $this->product->findOrFail($id);
    }
    
    public function update(int $id, array $data)
    {
        return $this->product->findOrFail($id)->update($data);
    }
    
    public function delete(int $id)
    {
        return $this->product->findOrFail($id)->delete();
    }
    
    public function findAll(array $data)
    {
        return $this->product->where($data)->get();
    }
}
