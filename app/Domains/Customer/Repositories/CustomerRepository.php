<?php

namespace App\Domains\Customer\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Customer\Entities\Customer;
use Illuminate\Support\Collection;

class CustomerRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly Customer $customer
    ) {
    }

    public function findById(int $id): Customer
    {
        return $this->customer->findOrFail($id);
    }

    public function findAll(array $filters): Collection
    {
        return $this->customer
        ->when(!empty($filters), function ($query) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->when($key === 'name', function ($q) use ($key, $value) {
                        return $q->where($key, 'LIKE', '%' . $value . '%');
                    }, function ($q) use ($key, $value) {
                        return $q->where($key, $value);
                    });
                }
            }
        })
        ->get();
    }

    public function create(array $data): Customer
    {
        return $this->customer->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->customer->findOrFail($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->customer->findOrFail($id)->delete();
    }
}
