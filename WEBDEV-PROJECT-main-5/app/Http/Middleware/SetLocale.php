<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for language parameter in URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            if (in_array($locale, ['en', 'id'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        }
        // Check session for stored language preference
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, ['en', 'id'])) {
                App::setLocale($locale);
            }
        }
        // Default to English if no preference is set
        else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
