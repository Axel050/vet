<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

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
}
