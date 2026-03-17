<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeterinarySocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_id',
        'platform',
        'url',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
