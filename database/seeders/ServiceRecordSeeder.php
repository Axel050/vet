<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\VeterinaryType;
use Illuminate\Database\Seeder;

class ServiceRecordSeeder extends Seeder
{
    public function run(): void
    {
        $services = VeterinaryType::all();

        Pet::all()->each(function ($pet) use ($services) {
            MedicalRecord::factory()->create([
                'veterinary_id' => $pet->veterinary_id,
                'pet_id' => $pet->id,
                'customer_id' => $pet->customer_id,
                'veterinary_type_id' => $services->random()->id,
            ]);
        });
    }
}
