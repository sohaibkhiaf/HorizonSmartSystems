<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale) {

        // switch
        if (in_array($locale, ['en', 'fr', 'ar'])) {

            session(['locale' => $locale]);
        }

        // back
        return redirect()->back();
    }
}
