<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Veterinary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VeterinarySeeder extends Seeder
{
    public function run(): void
    {

        $veterinary = Veterinary::create([
            'name' => 'Cruce',
            'slug' => 'cruce',
            'plan' => 'pro',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addDays(30),
        ]);

        User::create([
            'name' => 'cruce',
            'veterinary_id' => $veterinary->id,
            'email' => 'cruce@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'owner',
        ]);
    }
}
