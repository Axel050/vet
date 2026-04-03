<?php

namespace App\Livewire\Forms;

use App\Models\VeterinaryProfile as ProfileModel;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Form;

class VeterinaryProfile extends Form
{
    public ?ProfileModel $profile = null;

    public $hero_title = '';

    public $hero_subtitle = '';

    public $description = '';

    public $address = '';

    public $phone = '';

    public $whatsapp = '';

    public $logo;

    public $cover_image;

    public $years_in_business;

    public function setProfile(ProfileModel $profile): void
    {
        $this->profile = $profile;
        $this->hero_title = $profile->hero_title ?? '';
        $this->hero_subtitle = $profile->hero_subtitle ?? '';
        $this->description = $profile->description ?? '';
        $this->address = $profile->address ?? '';
        $this->phone = $profile->phone ?? '';
        $this->whatsapp = $profile->whatsapp ?? '';
        $this->years_in_business = $profile->years_in_business ?? '';
    }

    protected function rules(): array
    {
        return [
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5024', 'dimensions:max_width=2000,max_height=2000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10048', 'dimensions:max_width=2000,max_height=1000'],
            'years_in_business' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    protected function messages(): array
    {
        return [
            'hero_title.required' => 'El título es obligatorio.',
            'hero_title.min' => 'El título debe tener al menos 3 caracteres.',
            'hero_title.unique' => 'El título ya existe.',
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.max' => 'El logo no debe exceder los 5MB.',
            'cover_image.image' => 'La portada debe ser una imagen.',
            'cover_image.max' => 'La portada no debe exceder los 10MB.',
            'years_in_business.integer' => 'Los años deben ser un número entero.',
            'years_in_business.min' => 'Los años deben ser mayor o igual a 0.',
            'years_in_business.max' => 'Los años no pueden ser mayor a 100.',
        ];
    }

    public function store(): void
    {
        $this->validate();

        $whatsapp = preg_replace('/[^0-9]/', '', $this->whatsapp);

        $data = [
            'veterinary_id' => auth()->user()->veterinary_id,
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'whatsapp' => $whatsapp,
            'years_in_business' => $this->years_in_business ?? 0,
        ];

        if ($this->logo) {

            $photoPath = $this->logo->store('profiles/logos', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->cover(width: 200, height: 200);
            $image->save();

            $data['logo'] = $photoPath;
        }

        if ($this->cover_image) {

            $photoPath = $this->cover_image->store('profiles/covers', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->scale(width: 1240);
            $image->save();

            $data['cover_image'] = $photoPath;
        }

        ProfileModel::create($data);

        $this->reset(['logo', 'cover_image']);
    }

    public function update(): void
    {
        $this->validate();

        $whatsapp = preg_replace('/[^0-9]/', '', $this->whatsapp);

        $data = [
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'whatsapp' => $whatsapp,
            'years_in_business' => $this->years_in_business ?: 0,
        ];

        if ($this->logo) {
            if ($this->profile->logo && Storage::disk('public')->exists($this->profile->logo)) {
                Storage::disk('public')->delete($this->profile->logo);
            }

            $photoPath = $this->logo->store('profiles/logos', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->cover(width: 200, height: 200);
            $image->save();

            $data['logo'] = $photoPath;
        }

        if ($this->cover_image) {
            if ($this->profile->cover_image && Storage::disk('public')->exists($this->profile->cover_image)) {
                Storage::disk('public')->delete($this->profile->cover_image);
            }

            $photoPath = $this->cover_image->store('profiles/covers', 'public');

            $manager = new ImageManager(new Driver);
            $image = $manager->read(storage_path("app/public/{$photoPath}"));
            $image->scale(width: 1240);
            $image->save();

            $data['cover_image'] = $photoPath;
        }

        $this->profile->update($data);

        $this->reset(['logo', 'cover_image']);
    }

    public function removeLogo(): void
    {
        if ($this->profile && $this->profile->logo) {
            if (Storage::disk('public')->exists($this->profile->logo)) {
                Storage::disk('public')->delete($this->profile->logo);
            }
            $this->profile->update(['logo' => null]);
        }
        $this->reset('logo');
    }

    public function removeCoverImage(): void
    {
        if ($this->profile && $this->profile->cover_image) {
            if (Storage::disk('public')->exists($this->profile->cover_image)) {
                Storage::disk('public')->delete($this->profile->cover_image);
            }
            $this->profile->update(['cover_image' => null]);
        }
        $this->reset('cover_image');
    }
}
