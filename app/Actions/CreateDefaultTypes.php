<?php

namespace App\Actions;

use App\Models\Veterinary;
use App\Models\VeterinaryType;

class CreateDefaultTypes
{
    public static function handle(Veterinary $veterinary): void
    {
        $types = [
            [
                'name' => 'Control / Consulta',
                'description' => 'Consulta general de rutina',
                'icon' => 'stethoscope',
            ],
            [
                'name' => 'Vacunación',
                'description' => 'Aplicación de vacunas',
                'icon' => 'syringe',
            ],
            [
                'name' => 'Desparasitación',
                'description' => 'Tratamiento antiparasitario',
                'icon' => 'shield',
            ],
        ];

        foreach ($types as $type) {
            VeterinaryType::create([
                'veterinary_id' => $veterinary->id,
                'name' => $type['name'],
                'description' => $type['description'],
                'icon' => $type['icon'],
                'is_active' => true,
            ]);
        }
    }
}
