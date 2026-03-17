<?php

namespace Database\Factories;

use App\Models\Breed;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Species;
use App\Models\Veterinary;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'veterinary_id' => Veterinary::factory(),
            'customer_id' => Customer::factory(),
            'species_id' => Species::factory(),
            'breed_id' => Breed::factory(),
            'name' => $this->faker->firstName(),
            'birth_year' => $this->faker->year(),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Macho', 'Hembra']),
            'microchip_id' => $this->faker->unique()->numerify('################'),
            'color' => $this->faker->safeColorName(),
            'is_sterilized' => $this->faker->boolean(),
            'allergies' => $this->faker->optional()->sentence(),
            'chronic_medications' => $this->faker->optional()->sentence(),
            'weight' => $this->faker->randomFloat(2, 1, 50),
            'photo_path' => null,
            'blood_type' => $this->faker->randomElement(['A', 'B', 'AB', 'O', 'DEA 1.1', 'DEA 1.2']),
            'public_token' => (string) Str::uuid(),
        ];
    }
}
