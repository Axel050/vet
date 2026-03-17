<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVeterinariaIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! auth()->check()) {
            abort(403, 'Acceso no autorizado');
        }

        if (! $user || ! $user->veterinary) {
            abort(403);
        }

        if (! $user->veterinary->isActive()) {
            abort(402, 'Tu suscripción no está activa');
        }

        return $next($request);
    }
}
