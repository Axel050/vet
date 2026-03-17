<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Veterinary;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'staff',
            'veterinary_id' => Veterinary::factory(),
        ];
    }

    public function owner()
    {
        return $this->state(fn () => [
            'role' => 'owner',
        ]);
    }

    public function superAdmin()
    {
        return $this->state(fn () => [
            'role' => 'super_admin',
            'veterinary_id' => null,
        ]);
    }
}
