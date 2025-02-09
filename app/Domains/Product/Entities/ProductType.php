<?php

namespace App\Domains\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProductType extends Model
{
    use SoftDeletes;

    protected $table = 'product_types';
    protected $hidden = ['updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s'
    ];
    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
