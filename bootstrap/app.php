<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\Client;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->alias([
            'admin' => Admin::class,
            'client' => Client::class, 
        ]);

        $middleware->redirectGuestsTo(fn () => route('login.page'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
