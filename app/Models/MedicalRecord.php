<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'veterinary_id',
        'pet_id',
        'customer_id',
        'veterinary_type_id',
        'custom_type_name',
        'price',
        'notes',
        'notes_inside',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'anamnesis',
        'physical_exam_details',
        'diagnosis',
        'prognosis',
        'treatment_plan',
        'prescriptions',
        'recommendations',
        'next_appointment_at',
        'is_visible_to_owner',
        'weight',
        'performed_at',
    ];

    protected $casts = [
        'performed_at' => 'date',
        'next_appointment_at' => 'date',
        'is_visible_to_owner' => 'boolean',
        'temperature' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::deleting(function ($record) {
            if (! $record->isForceDeleting()) {
                foreach ($record->files as $file) {
                    $file->delete();
                }
            }
        });
    }

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function type()
    {
        return $this->belongsTo(VeterinaryType::class, 'veterinary_type_id');
    }

    public function files()
    {
        return $this->hasMany(MedicalRecordFile::class);
    }
}
