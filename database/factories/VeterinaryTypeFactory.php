<?php

namespace Database\Factories;

use App\Models\Veterinary;
use App\Models\VeterinaryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeterinaryTypeFactory extends Factory
{
    protected $model = VeterinaryType::class;

    public function definition(): array
    {
        return [
            'veterinary_id' => Veterinary::factory(),
            'name' => fake()->randomElement([
                'Consulta General',
                'Vacunación',
                'Desparasitación',
                'Cirugía',
                'Laboratorio',
            ]),
            'description' => fake()->sentence(),
            'icon' => null,
            'show_in_landing' => true,
            'is_active' => true,
        ];
    }
}
