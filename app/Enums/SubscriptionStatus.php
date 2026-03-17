<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::TRIAL => 'Prueba',
            self::ACTIVE => 'Activo',
            self::PAST_DUE => 'Vencido',
            self::SUSPENDED => 'Suspendido',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRIAL => 'bg-blue-500/10 text-blue-500',
            self::ACTIVE => 'bg-green-500/10 text-green-500',
            self::PAST_DUE => 'bg-yellow-500/10 text-yellow-500',
            self::SUSPENDED => 'bg-red-500/10 text-red-500',
            self::CANCELLED => 'bg-gray-500/10 text-gray-500',
        };
    }
}
