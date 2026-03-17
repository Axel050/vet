<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperadmin
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    if (!auth()->check() || !auth()->user()->is_superadmin) {
      abort(403, 'Acceso no autorizado');
    }

    return $next($request);
  }
}
