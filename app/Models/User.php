<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'veterinary_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($segment) => $segment[0])
            ->implode('');
    }

    /**
     * Get the pets for this veterinaria.
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Get the customers for this veterinaria.
     */
    public function customers()
    {
        return $this->hasMany(VeterinaryCustomer::class);
    }

    /**
     * Get the veterinaria profile.
     */
    public function veterinariaProfile()
    {
        return $this->hasOne(VeterinaryProfile::class);
    }

    public function getSubscriptionStatusAttribute()
    {
        return $this->veterinary?->subscription_status;
    }

    public function getIsSubscriptionActiveAttribute(): bool
    {
        return $this->veterinary && $this->veterinary->isActive();
    }

    public function veterinary()
    {
        return $this->belongsTo(Veterinary::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
