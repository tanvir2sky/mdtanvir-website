<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $locale = Locale::isSupported($locale) ? $locale : Locale::default();

        App::setLocale($locale);
        Carbon::setLocale($locale);
        View::share('locale', $locale);

        return $next($request);
    }
}
