<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale) {

        // switch
        if (in_array($locale, ['en', 'fr', 'ar'])) {

            session(['locale' => $locale]);

            // back + cookie
            return redirect()->back()
                ->withCookie(cookie('locale', $locale, 60 * 24 * 30));
        }

        // back
        return redirect()->back();
    }
}
