<?php

namespace Database\Seeders;

use App\Models\Veterinary;
use App\Models\VeterinaryType;
use Illuminate\Database\Seeder;

class VeterinaryTypeSeeder extends Seeder
{
    public function run(): void
    {
        $veterinary = Veterinary::first();

        $type = ['vacunas', 'control rutina', 'desparasitacion', 'castracion', 'cirugia', 'analitica', 'ecografia', 'radiografia', 'odontologia', 'nutricion', 'urgencias', 'hospitalizacion', 'peluqueria', 'asesoramiento'];

        foreach ($type as $t) {
            VeterinaryType::create([
                'veterinary_id' => $veterinary->id,
                'name' => $t,
                'description' => 'Servicio de '.$t,
                'is_active' => true,
            ]);
        }
    }
}
