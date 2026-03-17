<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleCamionCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [

    'Mercedes-Benz' => [
        'L 1114',
        'L 1518',
        'L 1620',
        'L 1634',
        'Atego',
        'Axor',
        'Actros',
        'Accelo',
        '1721',
        '1933',
    ],

    'Ford' => [
        'F-600',
        'F-700',
        'F-1000',
        'Cargo 1722',
        'Cargo 2428',
        'Cargo 1933',
        'Cargo 2042',
        'F-14000',
    ],

    'Volkswagen' => [
        'Delivery 9.170',
        'Delivery 11.180',
        'Constellation 17.280',
        'Constellation 24.280',
        'Constellation 25.360',
        'Worker 17.220',
        'Worker 13.180',
    ],

    'Scania' => [
        'L75',
        'L111',
        'T112',
        'R113',
        'Serie P',
        'Serie G',
        'Serie R',
        'Serie S',
    ],

    'IVECO' => [
        'Daily',
        'EuroCargo',
        'Tector',
        'Cursor',
        'Stralis',
        'Hi-Way',
    ],

    'Volvo' => [
        'F88',
        'NL10',
        'NH12',
        'FM',
        'FH',
        'VM',
    ],

    'Renault Trucks' => [
        'Midliner',
        'Premium',
        'Kerax',
        'T',
        'D',
    ],

    'Agrale' => [
        'A8700',
        'A10000',
        'A14000',
    ],

    'Chevrolet' => [
        'C-10',
        'C-60',
        'D-20',
    ],

    'Dodge' => [
        'DP-600',
        'DP-800',
        '1000',
    ],

    'Fiat' => [
        '619',
        '697',
    ],

    'Hino' => [
        'Serie 300',
        'Serie 500',
    ],

    'Isuzu' => [
        'NKR',
        'NPR',
        'FTR',
    ],

    'Foton' => [
        'Aumark',
        'Auman',
    ],

    'JAC' => [
        'N55',
        'N75',
        'HFC 1035',
    ],

    'Shacman' => [
        'X3000',
        'F3000',
    ],

];

        foreach ($brands as $brand => $models) {

            $brandModel = VehicleBrand::firstOrCreate(
                ['name' => $brand, 'type' => 'camion'],
                [
                    'is_custom' => false,
                    'type' => 'camion',
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
