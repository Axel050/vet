<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
                'veterinary_id' => null,
            ]
        );

        $this->call([
            VeterinarySeeder::class,
            VeterinaryTypeSeeder::class,
            SpecieSeeder::class,
            CustomerSeeder::class,

            // UserSeeder::class,
            // ServiceRecordSeeder::class,
        ]);

    }
}
