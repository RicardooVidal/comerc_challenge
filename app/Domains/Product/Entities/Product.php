<?php

namespace App\Domains\Product\Entities;

use App\Domains\Order\Entities\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s'
    ];
    protected $fillable = [
        'name',
        'price',
        'photo'
    ];

    protected $appends = ['photo_url'];

    public $timestamps = false;

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
