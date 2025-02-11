<?php

namespace App\Domains\Customer\Services;

use App\Domains\Customer\DTOs\CustomerDTO;
use App\Domains\Customer\Repositories\CustomerRepository;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    ) {
    }

    public function findById(int $id): array
    {
        return $this->customerRepository->findById($id)?->toArray();
    }

    public function findAll(array $filters): array
    {
        return $this->customerRepository->findAll($filters)->toArray();
    }

    public function create(CustomerDTO $dto): array
    {
        return $this->customerRepository->create($dto->toArray())->toArray();
    }

    public function update(int $id, CustomerDTO $dto): ?bool
    {
        return $this->customerRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): void
    {
        $this->customerRepository->delete($id);
    }
}
