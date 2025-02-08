<?php

namespace App\Domains\Customer\DTOs;

class CustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $birth_date,
        public readonly string $address,
        public readonly ?string $address_complement,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state,
        public readonly string $zip_code
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            birth_date: $data['birth_date'],
            address: $data['address'],
            address_complement: $data['address_complement'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            neighborhood: $data['neighborhood'],
            zip_code: $data['zip_code']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'address_complement' => $this->address_complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code
        ];
    }
} 