<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecordFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'file_path',
        'file_name',
        'original_name',
        'type',
        'is_visible_to_owner',
    ];

    /* ================= RELACIONES ================= */

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
