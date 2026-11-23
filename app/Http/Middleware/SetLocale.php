<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('idioma', config('app.locale'));

        // Fall back to the configured locale if the session contains an unsupported value.
        if (! is_string($locale) || ! in_array($locale, ['ca', 'en', 'es'], true)) {
            $locale = App::getLocale();
        }

        App::setLocale($locale);

        return $next($request);
    }
}
