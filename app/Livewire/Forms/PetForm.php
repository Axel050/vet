<?php

namespace App\Livewire\Forms;

use App\Models\BreedRequest;
use App\Models\Pet;
use App\Models\SpeciesRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Form;

class PetForm extends Form
{
    public ?Pet $pet = null;

    public $customer_id;

    public $species_id;

    public $specie_custom;

    public $breed_id;

    public $breed_custom;

    public $name;

    public $birth_year;

    public $date_of_birth;

    public $gender = 'unknown';

    public $microchip_id;

    public $color;

    public $is_sterilized = false;

    public $allergies;

    public $chronic_medications;

    public $weight;

    public $blood_type;

    public $photo;

    public function setPet(Pet $pet): void
    {
        $this->pet = $pet;
        $this->photo = null;
        $this->species_id = $pet->species_id ?: 'other';
        $this->specie_custom = $pet->specie_custom;
        $this->breed_id = $pet->breed_id ?: 'other';
        $this->breed_custom = $pet->breed_custom;
        $this->name = $pet->name;
        $this->birth_year = $pet->birth_year;
        $this->date_of_birth = $pet->date_of_birth?->format('Y-m-d');
        $this->gender = $pet->gender ?? 'unknown';
        $this->microchip_id = $pet->microchip_id;
        $this->color = $pet->color;
        $this->is_sterilized = (bool) $pet->is_sterilized;
        $this->allergies = $pet->allergies;
        $this->chronic_medications = $pet->chronic_medications;
        $this->weight = $pet->weight;
        $this->blood_type = $pet->blood_type;
    }

    public function rules(): array
    {
        return [
            'species_id' => 'required',
            'breed_id' => 'required',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pets')
                    ->where(function ($query) {
                        return $query->where('veterinary_id', Auth::user()->veterinary_id)
                            ->where('customer_id', $this->customer_id);
                    })
                    ->ignore($this->pet?->id),
            ],
            'birth_year' => 'nullable|integer|min:1990|max:'.date('Y'),
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,unknown',
            'microchip_id' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'is_sterilized' => 'boolean',
            'allergies' => 'nullable|string',
            'chronic_medications' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'blood_type' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'species_id' => 'especie',
            'breed_id' => 'raza',
            'name' => 'nombre',
            'birth_year' => 'año de nacimiento',
            'date_of_birth' => 'fecha de nacimiento',
            'gender' => 'sexo',
            'microchip_id' => 'ID de microchip',
            'color' => 'color',
            'is_sterilized' => 'esterilizado',
            'allergies' => 'alergias',
            'chronic_medications' => 'medicación crónica',
            'weight' => 'peso',
            'blood_type' => 'tipo de sangre',
            'photo' => 'foto',
        ];
    }

    public function messages(): array
    {
        return [
            'species_id.required' => 'La especie es obligatoria',
            'breed_id.required' => 'La raza es obligatoria',
            'name.required' => 'El nombre de la mascota es obligatorio',
            'name.unique' => 'Este cliente ya tiene una mascota con este nombre.',
            'birth_year.integer' => 'El año debe ser un número entero',
            'birth_year.min' => 'El año debe ser mayor o igual a 1990',
            'birth_year.max' => 'El año no puede ser futuro',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.max' => 'La foto no debe pesar más de 2MB.',
        ];
    }

    public function store(): Pet
    {
        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('pets', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->cover(600, 600);
            $image->save();
        }

        $speciesId = $this->species_id === 'other' ? null : $this->species_id;
        $breedId = $this->breed_id === 'other' ? null : $this->breed_id;

        $data = array_merge($this->except('pet', 'photo'), [
            'customer_id' => $this->customer_id,
            'veterinary_id' => Auth::user()->veterinary_id,
            'public_token' => Str::random(10),
            'species_id' => $speciesId,
            'specie_custom' => $speciesId === null ? $this->specie_custom : null,
            'breed_id' => $breedId,
            'breed_custom' => $breedId === null ? $this->breed_custom : null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'is_sterilized' => (bool) $this->is_sterilized,
            'photo_path' => $photoPath,
        ]);

        $pet = Pet::create($data);

        $this->handleSpeciesAndBreedRequests($speciesId, $breedId);

        return $pet;
    }

    public function update(): void
    {
        $this->validate();

        $photoPath = $this->pet->photo_path;
        if ($this->photo) {
            if ($this->pet->photo_path && Storage::disk('public')->exists($this->pet->photo_path)) {
                Storage::disk('public')->delete($this->pet->photo_path);
            }

            $photoPath = $this->photo->store('pets', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->cover(600, 600);
            $image->save();
        }

        $speciesId = $this->species_id === 'other' ? null : $this->species_id;
        $breedId = $this->breed_id === 'other' ? null : $this->breed_id;

        $data = array_merge($this->except('pet', 'photo'), [
            'species_id' => $speciesId,
            'breed_id' => $breedId,
            'specie_custom' => $speciesId === null ? $this->specie_custom : null,
            'breed_custom' => $breedId === null ? $this->breed_custom : null,
            'date_of_birth' => $this->date_of_birth ?: null,
            'is_sterilized' => (bool) $this->is_sterilized,
            'photo_path' => $photoPath,
        ]);

        $this->pet->update($data);

        $this->handleSpeciesAndBreedRequests($speciesId, $breedId);

    }

    private function handleSpeciesAndBreedRequests(?int $speciesId, ?int $breedId): void
    {
        if ($speciesId === null && $this->specie_custom) {

            SpeciesRequest::firstOrCreate([
                'name' => trim($this->specie_custom),
            ], [
                'veterinary_id' => Auth::user()->veterinary_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        }

        if ($breedId === null && $this->breed_custom) {

            BreedRequest::firstOrCreate([
                'name' => trim($this->breed_custom),
            ], [
                'species_id' => $speciesId,
                'custom_species' => $speciesId ? null : $this->specie_custom,
                'veterinary_id' => Auth::user()->veterinary_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        }
    }

    private function handleSpeciesAndBreedRequestss(?int $speciesId, ?int $breedId): void
    {

        info([
            'speciesId' => $speciesId,
            'breedId' => $breedId,
            'specie_custom' => $this->specie_custom,
            'breed_custom' => $this->breed_custom,
        ]);

        if ($speciesId === null && $this->specie_custom) {
            SpeciesRequest::firstOrCreate([
                'name' => trim($this->specie_custom),
            ], [
                'veterinary_id' => Auth::user()->veterinary_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        }

        if ($breedId === null && $this->breed_custom) {
            BreedRequest::firstOrCreate([
                'name' => trim($this->breed_custom),
            ], [
                'species_id' => $speciesId,
                'veterinary_id' => Auth::user()->veterinary_id,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);
        }
    }
}
