<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeterinaryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinary_id',
        'name',
        'description',
        'icon',
        'show_in_landing',
        'is_active',
    ];

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
