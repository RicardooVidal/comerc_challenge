<?php

namespace App\Domains\Customer\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';
    protected $hidden = ['updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'address',
        'address_complement',
        'neighborhood',
        'city',
        'state',
        'zip_code'
    ];
}
