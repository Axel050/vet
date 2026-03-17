<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleCatalogSeeder extends Seeder
{
    public function run(): void
    {
       $brands = [

    'Toyota' => [
        'Hilux',
        'Corolla',
        'Yaris',
        'Etios',
        'Corolla Cross',
        'Camry',
        'RAV4',
        'SW4' => 'Fortuner',
        'Land Cruiser',
        'Celica',
        'Prius',
        '4Runner',
    ],

    'Volkswagen' => [
        'Gol',
        'Gol Trend',
        'Polo',
        'Virtus',
        'Taos',
        'T-Cross',
        'Amarok',
        'Vento',
        'Passat',
        'Saveiro',
        'Fox',
        'Up',
        'Golf',
        'Bora',
        'Suran',
        'Gacel',
        'Senda',
        'Pointer',
    ],

    'Ford' => [
        'Falcon',
        'Ranger',
        'F-100',
        'Fiesta',
        'Ka',
        'EcoSport',
        'Focus',
        'Escort',
        'Orion',
        'Sierra',
        'Taunus',
        'Mondeo',
        'Territory',
        'Maverick',
        'Bronco Sport',
        'Kuga',
    ],

    'Chevrolet' => [
        'Corsa',
        'Classic',
        'Onix',
        'Prisma',
        'Cruze',
        'Astra',
        'Vectra',
        'Tracker',
        'S10',
        'Montana',
        'Spin',
        'Silverado',
        'Chevy',
    ],

    'Fiat' => [
        '600' => 'Fitito',
        '128',
        '147',
        'Duna',
        'Spazio',
        'Regatta',
        'Uno',
        'Palio',
        'Siena',
        'Punto',
        'Tipo',
        'Tempra',
        'Qubo',
        'Cronos',
        'Argo',
        'Mobi',
        'Strada',
        'Toro',
        'Pulse',
        'Fastback',
    ],

    'Renault' => [
        'Torino',
        '12',
        '11',
        '19',
        'Clio',
        'Megane',
        'Symbol',
        'Scenic',
        'Sandero',
        'Stepway',
        'Logan',
        'Kangoo',
        'Duster',
        'Kwid',
        'Captur',
        'Alaskan',
        'Fuego',
    ],

    'Peugeot' => [
        '504',
        '405',
        '306',
        '406',
        '407',
        '206',
        '207',
        '207 Compact',
        '208',
        '308',
        '2008',
        'Partner',
    ],

    'Citroen' => [
        '2CV' => '3CV / Dos Caballos',
        'ZX',
        'Xsara',
        'C3',
        'C4',
        'C4 Lounge',
        'C5',
        'Picasso',
        'C3 Aircross',
        'Aircross',
        'Berlingo',
        'Basalt',
    ],

    'Honda' => [
        'Civic',
        'Accord',
        'City',
        'Fit',
        'HR-V',
        'CR-V',
        'WR-V',
    ],

    'Nissan' => [
        'Frontier',
        'Versa',
        'March',
        'Sentra',
        'Tiida',
        'X-Trail',
        'Kicks',
    ],

    'Jeep' => [
        'Renegade',
        'Compass',
        'Cherokee',
        'Grand Cherokee',
        'Wrangler',
    ],

    'RAM' => [
        '1500',
        '2500',
        '3500',
        'Rampage',
    ],

    'Mercedes-Benz' => [
        'Sprinter',
        'Clase A',
        'Clase B',
        'Clase C',
        'Clase E',
        'GLA',
        'GLB',
        'GLC',
    ],

    'BMW' => [
        'Serie 1',
        'Serie 2',
        'Serie 3',
        'Serie 5',
        'X1',
        'X3',
        'X5',
    ],

    'Audi' => [
        'A1',
        'A3',
        'A4',
        'A5',
        'Q2',
        'Q3',
        'Q5',
    ],

    'Hyundai' => [
        'Accent',
        'i10',
        'i20',
        'HB20',
        'Elantra',
        'Creta',
        'Tucson',
        'Santa Fe',
    ],

    'Kia' => [
        'Rio',
        'Cerato',
        'Soul',
        'Seltos',
        'Sportage',
        'Sorento',
    ],

    'Mitsubishi' => [
        'L200',
        'Montero',
        'Outlander',
        'ASX',
    ],

    'Suzuki' => [
        'Fun',
        'Swift',
        'Baleno',
        'Grand Vitara',
        'Vitara',
        'Jimny',
    ],

    'Chery' => [
        'Tiggo 2',
        'Tiggo 4',
        'Tiggo 7',
        'Tiggo 8',
        'QQ',
    ],

    'Haval' => [
        'H6',
        'Jolion',
    ],

    'JAC' => [
        'S2',
        'S4',
        'S8',
        'T8',
    ],

    'BAIC' => [
        'X35',
        'X55',
    ],

    'Geely' => [
        'Emgrand',
        'GX3',
    ],

    'DS' => [
        'DS3',
        'DS4',
        'DS7',
    ],

    'Volvo' => [
        'S60',
        'S90',
        'XC40',
        'XC60',
        'XC90',
    ],

    'Land Rover' => [
        'Defender',
        'Discovery',
        'Discovery Sport',
        'Range Rover Evoque',
        'Range Rover Sport',
    ],

    'Subaru' => [
        'Impreza',
        'Forester',
        'XV',
        'Outback',
    ],

    'Alfa Romeo' => [
        '147',
        '156',
        'Giulietta',
        'Stelvio',
    ],

    'Dodge' => [
        '1500',
        'Journey',
    ],

    'Chrysler' => [
        'Neon',
        'PT Cruiser',
    ],

];
        foreach ($brands as $brand => $models) {

            $brandModel = VehicleBrand::firstOrCreate(
                ['name' => $brand],
                ['is_custom' => false]
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
