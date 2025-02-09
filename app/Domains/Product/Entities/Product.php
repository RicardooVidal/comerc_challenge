<?php

namespace App\Domains\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $hidden = ['updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s'
    ];
    protected $fillable = [
        'name',
        'description',
        'price',
        'photo',
        'product_type_id'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
