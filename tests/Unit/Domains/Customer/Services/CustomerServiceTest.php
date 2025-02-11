<?php

namespace Tests\Unit\Domains\Customer\Services;

use App\Domains\Customer\DTOs\CustomerDTO;
use App\Domains\Customer\Entities\Customer;
use App\Domains\Customer\Repositories\CustomerRepository;
use App\Domains\Customer\Services\CustomerService;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPUnit\Framework\TestCase;

class CustomerServiceTest extends TestCase
{
    private CustomerService $service;
    private CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = $this->createMock(CustomerRepository::class);
        $this->service = new CustomerService($this->repository);
    }

    public function test_find_customer_by_id(): void
    {
        // Arrange
        $id = 1;
        $customer = Customer::factory()->make()->toArray();
        
        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(new Customer($customer));

        // Act
        $result = $this->service->findById($id);

        // Assert        
        $this->assertEquals($customer, $result);
    }

    public function test_find_customer_by_id_not_found(): void
    {
        // Arrange
        $id = 1;

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willThrowException(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->service->findById($id);
    }

    public function test_find_all_customers(): void
    {
        // Arrange
        $customers = [
            [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '123456789',
                'address' => 'Test Address'
            ],
            [
                'name' => 'Test Customer 2',
                'email' => 'test2@example.com',
                'phone' => '987654321',
                'address' => 'Test Address 2'
            ]
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn(collect($customers));

        // Act
        $result = $this->service->findAll([]);

        // Assert
        $this->assertEquals($customers, $result);
    }

    public function test_create_customer(): void
    {
        // Arrange
        $customer = Customer::factory()->make()->toArray();

        $this->repository->expects($this->once())
            ->method('create')
            ->with($customer)
            ->willReturn(new Customer($customer));

        // Act
        $result = $this->service->create(new CustomerDTO(...$customer));

        // Assert
        $this->assertEquals($customer, $result);
    }

    public function test_update_customer(): void
    {
        // Arrange
        $customer = Customer::factory()->make();
        $customer->id = 1;

        $customerData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'birth_date' => $customer->birth_date,
            'address' => $customer->address,
            'address_complement' => $customer->address_complement,
            'neighborhood' => $customer->neighborhood,
            'city' => $customer->city,
            'state' => $customer->state,
            'zip_code' => $customer->zip_code
        ];

        $this->repository->expects($this->once())
            ->method('update')
            ->with($customer->id, $customerData)
            ->willReturn(true);

        // Act
        $result = $this->service->update($customer->id, new CustomerDTO(...$customerData));

        // Assert
        $this->assertEquals(true, $result);
    }

    public function test_update_customer_not_found(): void
    {
        // Arrange
        $customer = Customer::factory()->make();
        $customer->id = 1;

        $customerData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'birth_date' => $customer->birth_date,
            'address' => $customer->address,
            'address_complement' => $customer->address_complement,
            'neighborhood' => $customer->neighborhood,
            'city' => $customer->city,
            'state' => $customer->state,
            'zip_code' => $customer->zip_code
        ];

        $this->repository->expects($this->once())
            ->method('update')
            ->with($customer->id)
            ->willThrowException(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->service->update($customer->id, new CustomerDTO(...$customerData));
    }

    public function test_delete_customer(): void
    {
        // Arrange
        $customer = Customer::factory()->make();
        $customer->id = 1;

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($customer->id);

        $this->service->delete($customer->id);
    }
}