<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminEmail = config('app.admin_email', 'tanvir2sky@gmail.com');
        $user = Auth::user();

        if (! $user || $user->email !== $adminEmail) {
            abort(403);
        }

        return $next($request);
    }
}
