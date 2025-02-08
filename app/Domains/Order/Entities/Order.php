<?php

namespace App\Domains\Order\Entities;

use App\Domains\Customer\Entities\Customer;
use App\Domains\Product\Entities\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'customer_id',
        'product_id'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'orders_products')
            ->withPivot('quantity', 'price')
            ->select(['products.id', 'products.name', 'products.photo']);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)
            ->select(['customers.id', 'customers.name']);
    }
}
