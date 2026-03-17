<?php

namespace Database\Factories;

use App\Models\Veterinary;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VeterinaryFactory extends Factory
{
    protected $model = Veterinary::class;

    public function definition(): array
    {
        $name = fake()->unique()->company.' Veterinarias';

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.fake()->unique()->numberBetween(1, 999)),
            'plan' => 'free',
            'pet_limit' => 10,
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'subscription_ends_at' => null,
        ];
    }

    public function active()
    {
        return $this->state(fn () => [
            'subscription_status' => 'active',
            'plan' => 'basic',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addMonths(1),
        ]);
    }

    public function pro()
    {
        return $this->state(fn () => [
            'plan' => 'pro',
            'subscription_status' => 'active',
            'pet_limit' => null,
            'subscription_ends_at' => now()->addYear(1),
        ]);
    }
}
