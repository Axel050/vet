<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeterinaryProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_id',
        'hero_title',
        'hero_subtitle',
        'description',
        'address',
        'phone',
        'whatsapp',
        'logo',
        'cover_image',
        'years_in_business',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }
}
