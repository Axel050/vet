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
            'price' => fake()->randomFloat(2, 500, 5000),
            'is_active' => true,
        ];
    }
}
