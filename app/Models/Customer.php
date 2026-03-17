<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class, 'customer_id');
    }
}
