<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Veterinary;
use App\Models\VeterinaryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'veterinary_id' => Veterinary::factory(),
            'pet_id' => Pet::factory(),
            'customer_id' => Customer::factory(),
            'veterinary_type_id' => VeterinaryType::factory(),
            'custom_type_name' => null,
            'price' => $this->faker->randomFloat(2, 20, 200),
            'notes' => $this->faker->paragraph(),
            'notes_inside' => $this->faker->paragraph(),
            'temperature' => $this->faker->randomFloat(2, 37, 40),
            'heart_rate' => $this->faker->numberBetween(60, 160),
            'respiratory_rate' => $this->faker->numberBetween(10, 40),
            'anamnesis' => $this->faker->paragraph(),
            'physical_exam_details' => $this->faker->paragraph(),
            'diagnosis' => $this->faker->sentence(),
            'prognosis' => $this->faker->randomElement(['Bueno', 'Reservado', 'Malo']),
            'treatment_plan' => $this->faker->paragraph(),
            'prescriptions' => $this->faker->sentence(),
            'recommendations' => $this->faker->paragraph(),
            'next_appointment_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
            'is_visible_to_owner' => true,
            'weight' => $this->faker->randomFloat(2, 1, 50),
            'performed_at' => now(),
        ];
    }
}
