<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veterinary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'plan',
        'pet_limit',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'subscription_status' => SubscriptionStatus::class,
    ];

    /* ================= RELACIONES ================= */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function types()
    {
        return $this->hasMany(VeterinaryType::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function payments()
    {
        return $this->hasMany(VeterinaryPayment::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(VeterinarySocialLink::class);
    }

    /* ================= HELPERS ================= */

    public function owner()
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    public function profile()
    {
        return $this->hasOne(VeterinaryProfile::class);
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro';
    }

    public function isActive(): bool
    {
        if (in_array($this->subscription_status, [SubscriptionStatus::SUSPENDED, SubscriptionStatus::CANCELLED])) {
            return false;
        }

        if ($this->subscription_status === SubscriptionStatus::TRIAL) {
            return $this->trial_ends_at?->isFuture() ?? false;
        }

        if (in_array($this->subscription_status, [SubscriptionStatus::ACTIVE, SubscriptionStatus::PAST_DUE])) {
            return true;
        }

        return false;
    }

    public function syncSubscriptionStatus(): void
    {
        if ($this->subscription_status === SubscriptionStatus::TRIAL && $this->trial_ends_at?->isPast()) {
            $this->update(['subscription_status' => SubscriptionStatus::SUSPENDED]);

            return;
        }

        if (in_array($this->subscription_status, [SubscriptionStatus::ACTIVE, SubscriptionStatus::PAST_DUE]) && $this->subscription_ends_at?->isPast()) {
            $daysPast = $this->subscription_ends_at->diffInDays(now(), false);
            if ($daysPast > 7) {
                $this->update(['subscription_status' => SubscriptionStatus::SUSPENDED]);
            } else {
                $this->update(['subscription_status' => SubscriptionStatus::PAST_DUE]);
            }
        }

        if ($this->subscription_status === SubscriptionStatus::SUSPENDED) {
            if ($this->plan === 'free') {
                $daysPast = $this->trial_ends_at->diffInDays(now(), false);
            } else {
                $daysPast = $this->subscription_ends_at->diffInDays(now(), false);
            }
            if ($daysPast > 20) {
                $this->update(['subscription_status' => SubscriptionStatus::CANCELLED]);
            }
        }
    }

    public function getEffectiveEndDateAttribute()
    {
        if ($this->plan === 'free' && $this->subscription_status === SubscriptionStatus::TRIAL) {
            return $this->trial_ends_at;
        }

        return $this->subscription_ends_at;
    }

    public function daysLeft(): ?int
    {
        if ($this->subscription_ends_at && $this->subscription_status === SubscriptionStatus::ACTIVE) {
            // info('OKOKOK');
            // info($this->subscription_ends_at);
            // info(now());
            // info('NOWWWWWWW ');

            // info($this->subscription_ends_at->diffInDays(now(), false));

            return now()->diffInDays($this->subscription_ends_at, false);
        }

        if ($this->trial_ends_at && $this->subscription_status === SubscriptionStatus::TRIAL) {
            // info('TRIEALLLLL');

            return now()->diffInDays($this->trial_ends_at, false);
        }

        return null;
    }

    public function getDaysLeftAttribute(): ?int
    {
        $endDate = $this->effective_end_date;
        if (! $endDate) {
            return null;
        }

        return now()->startOfDay()->diffInDays($endDate->copy()->startOfDay(), false);
    }

    public function getDaysLeftColorAttribute(): string
    {
        if ($this->days_left === null) {
            return 'text-gray-400';
        }
        if ($this->days_left <= 0) {
            return 'text-red-500 font-bold';
        }
        if ($this->subscription_status === SubscriptionStatus::TRIAL) {
            if ($this->days_left <= 3) {
                return 'text-yellow-500 font-bold';
            }

            return 'text-gray-400';
        }
        if ($this->days_left <= 7) {
            return 'text-yellow-500 font-bold';
        }

        return 'text-gray-400';
    }
}
