<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the language from Accept-Language header
        $acceptLanguage = $request->headers->get('Accept-Language');
        
        // Extract the language code (ar, en, etc.)
        $lang = $this->parseAcceptLanguage($acceptLanguage);

        // Set the application locale
        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        } else {
            // Default to English if language not supported
            app()->setLocale('en');
        }

        return $next($request);
    }

    /**
     * Parse Accept-Language header and return the best match
     * 
     * @param string|null $acceptLanguage
     * @return string
     */
    private function parseAcceptLanguage(?string $acceptLanguage): string
    {
        if (!$acceptLanguage) {
            return 'en';
        }

        // Parse the Accept-Language header
        // Example: "ar-SA,ar;q=0.9,en-US;q=0.8,en;q=0.7"
        $languages = [];
        
        foreach (explode(',', $acceptLanguage) as $language) {
            $parts = explode(';', $language);
            $lang = trim($parts[0]);
            
            // Extract just the language code (ar, en, etc.)
            $langCode = explode('-', $lang)[0];
            
            // Get quality factor (default 1.0)
            $quality = 1.0;
            if (isset($parts[1])) {
                preg_match('/q=([0-9.]+)/', $parts[1], $matches);
                if (isset($matches[1])) {
                    $quality = (float) $matches[1];
                }
            }

            $languages[$langCode] = $quality;
        }

        // Sort by quality in descending order
        arsort($languages);

        // Return the highest quality language that we support
        foreach (array_keys($languages) as $lang) {
            if (in_array($lang, ['ar', 'en'])) {
                return $lang;
            }
        }

        return 'en';
    }
}
