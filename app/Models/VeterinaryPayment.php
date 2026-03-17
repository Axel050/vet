<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeterinaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
