<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleMotoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Honda' => [
                'CG 150 Titan',
                'Wave 110',
                'XR 150L',
                'XR 250 Tornado',
                'CB 190R',
                'Biz 125',
                'CB 125F',
            ],

            'Yamaha' => [
                'FZ 16',
                'FZ-S FI',
                'YBR 125',
                'Crypton 110',
                'XTZ 125',
                'XTZ 250',
                'MT-03',
                'R3',
            ],

            'Zanella' => [
                'ZR 150',
                'RX 150',
                'Patagonian Eagle 150',
                'ZB 110',
                'Styler 150',
            ],

            'Motomel' => [
                'B110',
                'S2 150',
                'Skua 150',
                'Skua 250',
                'Blitz 110',
            ],

            'Corven' => [
                'Energy 110',
                'Triax 150',
                'Triax 250',
                'Touring 250',
            ],

            'Gilera' => [
                'Smash 110',
                'VC 150',
                'VC 200',
                'AC1 110',
            ],

            'Bajaj' => [
                'Rouser NS 125',
                'Rouser NS 160',
                'Rouser NS 200',
                'Rouser RS 200',
                'Avenger 220',
                'Dominar 250',
                'Dominar 400',
            ],

            'Kawasaki' => [
                'Ninja 300',
                'Ninja 400',
                'Z400',
                'Versys 300',
            ],

            'Suzuki' => [
                'AX 100',
                'GN 125',
                'EN 125',
                'GSX-R150',
                'V-Strom 650',
            ],

            'KTM' => [
                'Duke 200',
                'Duke 250',
                'Duke 390',
                'RC 200',
                'RC 390',
            ],

            'Benelli' => [
                'TNT 15',
                'TNT 25',
                'TNT 300',
                'TRK 502',
            ],

            'CFMoto' => [
                '300NK',
                '400NK',
                '650NK',
                '650MT',
            ],

            'Kymco' => [
                'Agility 125',
                'Like 125',
                'Downtown 300',
            ],

            'Guerrero' => [
                'G110 Trip',
                'GXR 250',
                'Urban 150',
            ],

            'Keller' => [
                'Crono 110',
                'Stratus 150',
            ],
        ];

        foreach ($brands as $brand => $models) {

            $brandModel = VehicleBrand::firstOrCreate(
                ['name' => $brand, 'type' => 'moto'],
                [
                    'is_custom' => false,
                    'type' => 'moto',
                ]
            );

            foreach ($models as $model) {
                VehicleModel::firstOrCreate([
                    'vehicle_brand_id' => $brandModel->id,
                    'name' => $model,
                ]);
            }
        }
    }
}
