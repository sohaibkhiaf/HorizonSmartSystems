<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/home', ['active' => 'accueil']);
});

Route::get('/about', function () {
    return view('/about', ['active' => 'apropos']);
});

Route::get('/services', function () {
    return view('/services', ['active' => 'services']);
});

Route::get('/methods', function () {
    return view('/methods', ['active' => 'methode']);
});

Route::get('/realisations', function () {
    return view('/realisations', ['active' => 'realisations']);
});

Route::get('/contact', function () {
    return view('/contact', ['active' => 'contact']);
});

