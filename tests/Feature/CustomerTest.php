<?php

namespace Tests\Feature;

use App\Domains\Customer\Entities\Customer;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->signIn();
    }

    public function test_can_list_customers(): void
    {
        Customer::factory(10)->create();

        $response = $this->getJson('/api/customers');

        $response->assertOk();
    }

    public function test_can_show_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson('/api/customers/1');

        $response->assertOk();

        $this->assertJson(json_encode($customer->toArray()));
    }

    public function test_can_create_customer(): void
    {
        $customer = Customer::factory()->make();
        $customerData = $customer->toArray();

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertCreated();

        $this->assertDatabaseHas('customers', [
            'name' => $customerData['name'],
            'email' => $customerData['email'],
            'phone' => $customerData['phone'],
            'birth_date' => $customerData['birth_date'],
            'address' => $customerData['address'],
            'address_complement' => $customerData['address_complement'],
            'neighborhood' => $customerData['neighborhood'],
            'city' => $customerData['city'],
            'state' => $customerData['state'],
            'zip_code' => $customerData['zip_code']
        ]);
    }

    public function test_cannot_create_customer_with_invalid_email(): void
    {
        $customer = Customer::factory()->make()->toArray();
        $customer['email'] = 'invalid-mail';

        $response = $this->postJson('/api/customers', $customer);

        $response->assertStatus(422);
    }
    
    public function test_can_update_customer(): void
    {
        $customer = Customer::factory()->create();

        $updatedCustomer = [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'birth_date' => '1998-01-01',
            'address' => 'Updated Address',
            'address_complement' => 'Updated Complement',
            'neighborhood' => 'Updated Neighborhood',
            'city' => 'City Updated',
            'state' => 'XX',
            'zip_code' => '00000000'
        ];

        $response = $this->putJson('/api/customers/1', $updatedCustomer);

        $response->assertOk();

        $customer = Customer::find(1);

        $this->assertJson(json_encode($customer->toArray()));
        $this->assertEquals('Updated Customer', $customer->name);
        $this->assertEquals('updated@example.com', $customer->email);
        $this->assertEquals('9876543210', $customer->phone);
        $this->assertEquals('1998-01-01', $customer->birth_date);
        $this->assertEquals('Updated Address', $customer->address);
        $this->assertEquals('Updated Complement', $customer->address_complement);
        $this->assertEquals('Updated Neighborhood', $customer->neighborhood);
        $this->assertEquals('City Updated', $customer->city);
        $this->assertEquals('XX', $customer->state);
        $this->assertEquals('00000000', $customer->zip_code);
    }

    public function test_cannot_update_customer_with_invalid_data(): void
    {
        $customer = Customer::factory()->create();
    
        $response = $this->putJson("/api/customers/{$customer->id}", []);
    
        $response->assertStatus(422);
    }
    
    public function test_can_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('customers', [
            'id' => $customer->id
        ]);
    }

    public function test_cannot_delete_customer_not_found(): void
    {
        $response = $this->deleteJson('/api/customers/9999');
        $response->assertNotFound();
    }
}
