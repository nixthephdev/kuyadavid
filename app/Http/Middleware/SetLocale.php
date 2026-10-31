<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const LOCALES = ['en', 'fil'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (in_array($locale, self::LOCALES, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
