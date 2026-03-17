<?php

namespace App\Providers;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerGates();

    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    protected function registerGates(): void
    {
        Gate::define('manage-veterinarias', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('pro-veterinaria', function (User $user) {
            return $user->veterinary?->plan === 'pro';
        });

        Gate::define('active-veterinaria', function (User $user) {
            if ($user->veterinary_id == null) {
                return false;
            }

            return in_array(
                $user->veterinary?->subscription_status,
                [
                    SubscriptionStatus::TRIAL,
                    SubscriptionStatus::ACTIVE,
                    SubscriptionStatus::PAST_DUE,
                ],
                true
            );
        });
    }
}
