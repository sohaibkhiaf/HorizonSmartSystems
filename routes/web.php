<?php

use Illuminate\Support\Facades\Route;

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


// hello from work
