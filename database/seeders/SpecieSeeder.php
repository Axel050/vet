<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpecieSeeder extends Seeder
{
    public function run()
    {
        $speciesBreeds = [
            'Perro' => [
                'Labrador Retriever',
                'Golden Retriever',
                'Pastor Alemán',
                'Bulldog',
                'Bulldog Francés',
                'Poodle',
                'Chihuahua',
                'Beagle',
                'Rottweiler',
                'Yorkshire Terrier',
                'Dachshund (Salchicha)',
                'Boxer',
                'Schnauzer',
                'Shih Tzu',
                'Border Collie',
                'Pitbull',
                'Pug',
                'Mestizo',
            ],

            'Gato' => [
                'Persa',
                'Siamés',
                'Maine Coon',
                'Bengalí',
                'Sphynx',
                'Ragdoll',
                'British Shorthair',
                'Angora',
                'Azul Ruso',
                'Mestizo',
            ],

            'Ave' => [
                'Canario',
                'Periquito',
                'Loro',
                'Cacatúa',
                'Agapornis',
                'Diamante mandarín',
            ],

            'Conejo' => [
                'Cabeza de León',
                'Rex',
                'Mini Lop',
                'Holandés',
                'Angora',
            ],

            'Hámster' => [
                'Sirio',
                'Ruso',
                'Roborovski',
                'Chino',
            ],

            'Tortuga' => [
                'Tortuga de agua',
                'Tortuga terrestre',
            ],

            'Hurón' => [
                'Hurón doméstico',
            ],

            'Reptil' => [
                'Iguana',
                'Gecko',
                'Serpiente',
            ],
        ];

        foreach ($speciesBreeds as $speciesName => $breeds) {

            $species = Species::create([
                'name' => $speciesName,
            ]);

            foreach ($breeds as $breedName) {

                Breed::create([
                    'name' => $breedName,
                    'species_id' => $species->id,
                ]);

            }
        }
    }
}
