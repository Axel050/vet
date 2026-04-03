<?php

namespace App\Livewire\Forms;

use App\Actions\CreateDefaultTypes;
use App\Enums\SubscriptionStatus;
use App\Models\User;
use App\Models\Veterinary;
use App\Models\VeterinaryProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VeterinaryForm extends Form
{
    public ?Veterinary $veterinary = null;

    // Veterinaria Fields
    public string $businessName = '';

    public string $plan = 'free';

    public $subscriptionStatus = 'trial';

    public string $slug = '';

    // User (Owner) Fields
    public string $name = '';

    public string $email = '';

    public string $password = '';

    // Profile Fields
    public string $address = '';

    public string $phone = '';

    public string $trialEndsAt = '';

    public string $subscriptionEndsAt = '';

    public function setVeterinary(Veterinary $veterinary): void
    {
        $this->veterinary = $veterinary;

        $this->businessName = $veterinary->name ?? '';
        $this->plan = $veterinary->plan ?? 'free';
        $this->subscriptionStatus = $veterinary->subscription_status?->value ?? 'trial';
        $this->trialEndsAt = $veterinary->trial_ends_at ? $veterinary->trial_ends_at->format('Y-m-d') : '';
        $this->subscriptionEndsAt = $veterinary->subscription_ends_at ? $veterinary->subscription_ends_at->format('Y-m-d') : '';
        $this->slug = $veterinary->slug ?? '';

        $owner = $veterinary->owner ?? null;
        if ($owner) {
            $this->name = $owner->name ?? '';
            $this->email = $owner->email ?? '';
        }

        $profile = $veterinary->profile ?? null;
        if ($profile) {
            $this->address = $profile->address ?? '';
            $this->phone = $profile->phone ?? '';
        }
    }

    protected function rules(): array
    {
        $owner = $this->veterinary ? $this->veterinary->owner : null;
        $emailRule = $owner ? "required|email|unique:users,email,{$owner->id}" : 'required|email|unique:users,email';

        return [
            // Veterinaria
            'businessName' => 'required|string|min:3|max:255',
            'plan' => 'required|in:free,basic,pro',
            'subscriptionStatus' => ['required', Rule::enum(SubscriptionStatus::class)],
            'slug' => $this->veterinary ? 'nullable' : 'required|alpha_dash|unique:veterinaries,slug',

            // Owner
            'name' => 'required|string|min:3|max:255',
            'email' => $emailRule,
            'password' => $this->veterinary ? 'nullable|string|min:8' : 'required|string|min:8',

            // Profile
            'address' => 'nullable|string|min:5',
            'phone' => 'nullable|string|min:8',

            // Dates
            'trialEndsAt' => 'nullable|date',
            'subscriptionEndsAt' => 'nullable|date',
        ];
    }

    protected function messages(): array
    {
        return [
            'businessName.required' => 'El nombre es obligatorio.',
            'businessName.min' => 'El nombre debe tener al menos 3 caracteres.',

            'plan.required' => 'Debes seleccionar un plan.',
            'plan.in' => 'El plan seleccionado no es válido.',

            'subscriptionStatus.required' => 'Debes indicar el estado de suscripción.',
            'subscriptionStatus.in' => 'Estado de suscripción inválido.',

            'name.required' => 'El nombre del propietario es obligatorio.',
            'phone.min' => 'El numero debe tener al menos 8 numeros.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no es válido.',
            'email.unique' => 'Este email ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

            'trialEndsAt.date' => 'La fecha de trial no es válida.',
            'subscriptionEndsAt.date' => 'La fecha de suscripción no es válida.',

            'slug.required' => 'El slug es obligatorio.',
            'slug.alpha_dash' => 'El slug solo puede contener letras, números y guiones.',
            'slug.unique' => 'Este slug ya está en uso.',
        ];
    }

    public function store(): void
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Create Veterinaria
            $veterinariaData = [
                'name' => $this->businessName,
                'slug' => $this->generateUniqueSlug($this->businessName),
                'plan' => $this->plan,
                'subscription_status' => $this->subscriptionStatus,
                'subscription_ends_at' => $this->subscriptionEndsAt ?: null,
                'pet_limit' => 100,
                'trial_ends_at' => $this->trialEndsAt ?: null,
            ];

            $veterinary = Veterinary::create($veterinariaData);

            // 2. Create Owner User
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'owner',
                'veterinary_id' => $veterinary->id,
            ]);

            // 3. Create Profile
            VeterinaryProfile::create([
                'veterinary_id' => $veterinary->id,
                'address' => $this->address,
                'phone' => $this->phone,
            ]);

            if (! $veterinary->types()->exists()) {
                CreateDefaultTypes::handle($veterinary);
            }
        });

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Update Veterinaria
            $this->veterinary->update([
                'name' => $this->businessName,
                'plan' => $this->plan,
                'subscription_status' => $this->subscriptionStatus,
                'subscription_ends_at' => $this->subscriptionEndsAt ?: null,
                'trial_ends_at' => $this->trialEndsAt ?: null,
            ]);

            // 2. Update Owner User
            $owner = $this->veterinary->owner;
            if ($owner) {
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                ];

                if ($this->password) {
                    $userData['password'] = Hash::make($this->password);
                }

                $owner->update($userData);
            }

            // 3. Update Profile
            $profile = VeterinaryProfile::firstOrNew(['veterinary_id' => $this->veterinary->id]);
            $profile->address = $this->address;
            $profile->phone = $this->phone;
            $profile->save();
        });

        $this->reset();
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = str($name)->slug();
        $slug = $base;
        $i = 1;

        while (Veterinary::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
