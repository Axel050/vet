<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'veterinary_id',
        'customer_id',
        'species_id',
        'specie_custom',
        'breed_id',
        'breed_custom',
        'name',
        'birth_year',
        'date_of_birth',
        'gender',
        'microchip_id',
        'color',
        'is_sterilized',
        'allergies',
        'chronic_medications',
        'weight',
        'photo_path',
        'blood_type',
        'public_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_sterilized' => 'boolean',
        'weight' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::deleting(function ($pet) {
            // Solo en soft delete
            if (! $pet->isForceDeleting()) {
                foreach ($pet->medicalRecords as $record) {
                    $record->delete(); // soft delete
                }
            }
        });
    }

    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age.' años';
        }

        if ($this->birth_year) {
            $age = date('Y') - $this->birth_year;

            return $age.' años (aprox.)';
        }

        return 'Edad desconocida';
    }

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getSpecieNameAttribute()
    {
        return $this->species->name ?? $this->specie_custom;
    }

    public function getBreedNameAttribute()
    {
        return $this->breed->name ?? $this->breed_custom;
    }
}
