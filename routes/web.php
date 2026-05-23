<?php

use Illuminate\Support\Facades\Route;


Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language-switcher');


Route::get('/', function () {
    return view('/home', ['page' => 'home']);
});

Route::get('/about', function () {
    return view('/about', ['page' => 'about']);
});

Route::get('/services', function () {
    return view('/services', ['page' => 'services']);
});

Route::get('/achievements', function () {
    return view('/achievements', ['page' => 'achievements']);
});

Route::get('/contact', function () {
    return view('/contact', ['page' => 'contact']);
});

Route::get('/services/webdev', function () {
    return view('/services/webdev', ['page' => 'services']);
});

