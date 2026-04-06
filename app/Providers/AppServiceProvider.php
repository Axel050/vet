<?php

namespace App\Providers;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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

        Mail::extend('brevo', function (array $config) {
            return (new BrevoTransportFactory)->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });

        $this->configureDefaults();
        $this->registerGates();

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Restablecer tu contraseña de '.config('app.name'))
                ->view('emails.password-reset', [
                    'url' => $url,
                    'user' => $notifiable,
                ]);
        });
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
            return $user->veterinary?->plan === 'pro' || $user->veterinary?->plan === 'free';
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
