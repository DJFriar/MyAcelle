<?php

namespace Acelle\Http\Middleware;

use Closure;

class NotLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $default_language = \Acelle\Model\Language::find(\Acelle\Model\Setting::get('default_language'));
        if (isset($_COOKIE['last_language_code'])) {
            $language_code = $_COOKIE['last_language_code'];
        } elseif ($default_language) {
            $language_code = $default_language->code;
        } else {
            $language_code = 'en';
        }

        // Language
        if ($language_code) {
            \App::setLocale($language_code);
            \Carbon\Carbon::setLocale($language_code);
        }

        return $next($request);
    }
}
