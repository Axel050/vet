<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeciesRequest extends Model
{
    /** @use HasFactory<\Database\Factories\SpeciesRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'veterinary_id',
        'user_id',
        'status',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
