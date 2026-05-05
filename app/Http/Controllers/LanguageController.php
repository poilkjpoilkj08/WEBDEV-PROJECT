<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate the locale
        if (!in_array($locale, ['en', 'id'])) {
            $locale = 'en'; // Default to English
        }

        // Store the locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Redirect back to the previous page
        return redirect()->back();
    }

    /**
     * Get the current locale
     */
    public function getCurrentLocale()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'locale_name' => App::getLocale() === 'en' ? 'English' : 'Bahasa Indonesia'
        ]);
    }
}
