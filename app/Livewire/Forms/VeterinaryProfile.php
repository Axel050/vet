<?php

namespace App\Livewire\Forms;

use App\Models\VeterinaryProfile as ProfileModel;
use Illuminate\Support\Facades\Storage;
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

    public function setProfile(ProfileModel $profile): void
    {
        $this->profile = $profile;
        $this->hero_title = $profile->hero_title ?? '';
        $this->hero_subtitle = $profile->hero_subtitle ?? '';
        $this->description = $profile->description ?? '';
        $this->address = $profile->address ?? '';
        $this->phone = $profile->phone ?? '';
        $this->whatsapp = $profile->whatsapp ?? '';
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
        ];

        if ($this->logo) {
            $data['logo'] = $this->logo->store('profiles/logos', 'public');
        }

        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('profiles/covers', 'public');
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
        ];

        if ($this->logo) {
            if ($this->profile->logo && Storage::disk('public')->exists($this->profile->logo)) {
                Storage::disk('public')->delete($this->profile->logo);
            }
            $data['logo'] = $this->logo->store('profiles/logos', 'public');
        }

        if ($this->cover_image) {
            if ($this->profile->cover_image && Storage::disk('public')->exists($this->profile->cover_image)) {
                Storage::disk('public')->delete($this->profile->cover_image);
            }
            $data['cover_image'] = $this->cover_image->store('profiles/covers', 'public');
        }

        $this->profile->update($data);

        $this->reset(['logo', 'cover_image']);
    }
}
