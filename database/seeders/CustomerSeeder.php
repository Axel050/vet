<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {

        $customers = [
            [
                'veterinary_id' => 1,
                'name' => 'Rayan',
                'phone' => '12345678',
                'email' => 'cual@54555.com',
                'address' => 'Calle 123',
            ],

            [
                'veterinary_id' => 1,
                'name' => 'Sofia',
                'phone' => '12345678',
                'email' => 'EMAIL@ADDRESS',
                'address' => 'Calle 123',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
