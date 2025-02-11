<?php

namespace Tests\Unit\Domains\Product\Services;

use App\Domains\Product\DTOs\ProductDTO;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class ProductServiceTest extends TestCase
{
    private ProductService $service;
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        
        $this->repository = $this->createMock(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }

    public function test_find_product_by_id(): void
    {
        $id = 1;
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => null,
            'photo_url' => null
        ];
        
        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(new Product($product));

        $result = $this->service->findById($id);

        $this->assertEquals($product, $result);
    }

    public function test_find_product_by_id_not_found(): void
    {
        $id = 1;

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willThrowException(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->service->findById($id);
    }

    public function test_find_all_products(): void
    {
        $products = [
            [
                'name' => 'Test Product',
                'price' => 100.0,
                'product_type_id' => 1,
                'photo' => null,
                'photo_url' => null
            ],
            [
                'name' => 'Test Product 2',
                'price' => 200.0,
                'product_type_id' => 1,
                'photo' => null,
                'photo_url' => null
            ]
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn(collect($products));

        $result = $this->service->findAll([]);

        $this->assertEquals($products, $result);
    }

    public function test_create_product_without_photo(): void
    {
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];
        
        $dto = ProductDTO::fromArray($product);
        $expectedProduct = new Product($product);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($product)
            ->willReturn($expectedProduct);

        $result = $this->service->create($dto);

        $this->assertEquals($expectedProduct->toArray(), $result);
    }

    public function test_create_product_with_photo(): void
    {
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];

        $photo = Mockery::mock(UploadedFile::class);
        $photo->shouldReceive('hashName')->andReturn('test-hash.png');
        $photo->shouldReceive('store')
            ->with('produtos', 'public')
            ->andReturn('produtos/test-hash.png');
        
        $dto = ProductDTO::fromArray($product);
        $expectedProduct = new Product([...$product, 'photo' => 'produtos/test-hash.png']);

        $this->repository->expects($this->once())
            ->method('create')
            ->with([...$product, 'photo' => 'produtos/test-hash.png'])
            ->willReturn($expectedProduct);

        $result = $this->service->create($dto, $photo);

        $this->assertEquals($expectedProduct->toArray(), $result);
    }

    public function test_update_product_with_photo_deletes_old_photo(): void
    {
        $id = 1;
        $oldPhoto = 'produtos/old-photo.png';
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => $oldPhoto
        ];

        Storage::disk('public')->put($oldPhoto, 'fake content');
        
        $existingProduct = new Product($product);
        $newPhoto = Mockery::mock(UploadedFile::class);
        $newPhoto->shouldReceive('hashName')->andReturn('new-test-hash.png');
        $newPhoto->shouldReceive('store')
            ->with('produtos', 'public')
            ->andReturn('produtos/new-test-hash.png');
        
        $dto = ProductDTO::fromArray($product);

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($existingProduct);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($id, [...$product, 'photo' => 'produtos/new-test-hash.png'])
            ->willReturn(true);

        $result = $this->service->update($id, $dto, $newPhoto);

        Storage::disk('public')->assertMissing($oldPhoto);
        $this->assertTrue($result);
    }

    public function test_update_product_without_photo_keeps_old_photo(): void
    {
        $id = 1;
        $oldPhoto = 'produtos/old-photo.png';
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => $oldPhoto
        ];

        Storage::disk('public')->put($oldPhoto, 'fake content');
        
        $dto = ProductDTO::fromArray($product);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($id, [
                'name' => 'Test Product',
                'price' => 100.0,
                'product_type_id' => 1,
            ])
            ->willReturn(true);

        $result = $this->service->update($id, $dto);

        Storage::disk('public')->assertExists($oldPhoto);
        $this->assertTrue($result);
    }

    public function test_update_product_not_found(): void
    {
        $id = 1;
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];
        
        $dto = ProductDTO::fromArray($product);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($id)
            ->willThrowException(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->service->update($id, $dto);
    }

    public function test_update_product_without_previous_photo(): void
    {
        $id = 1;
        $product = [
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];
        
        $existingProduct = new Product($product);
        $dto = ProductDTO::fromArray($product);
        
        $newPhoto = Mockery::mock(UploadedFile::class);
        $newPhoto->shouldReceive('hashName')->andReturn('new-test-hash.png');
        $newPhoto->shouldReceive('store')
            ->with('produtos', 'public')
            ->andReturn('produtos/new-test-hash.png');

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($existingProduct);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($id, [...$product, 'photo' => 'produtos/new-test-hash.png'])
            ->willReturn(true);

        $result = $this->service->update($id, $dto, $newPhoto);

        $this->assertTrue($result);
    }

    public function test_delete_product_deletes_photo(): void
    {
        $id = 1;
        $photo = 'produtos/product.png';
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => $photo
        ]);

        Storage::disk('public')->put($photo, 'fake content');

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($product);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($id);

        $this->service->delete($id);

        Storage::disk('public')->assertMissing($photo);
    }

    public function test_delete_nonexistent_product(): void
    {
        $id = 1;

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willThrowException(new ModelNotFoundException());

        $this->repository->expects($this->never())
            ->method('delete');

        $this->expectException(ModelNotFoundException::class);
        
        $this->service->delete($id);
    }

    public function test_delete_product_without_photo(): void
    {
        $id = 1;
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.0,
            'product_type_id' => 1
        ]);

        $this->repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn($product);

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($id);

        $this->service->delete($id);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
