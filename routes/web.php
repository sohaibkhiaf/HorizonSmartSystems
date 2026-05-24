<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Models\Achievement;
use Illuminate\Support\Facades\Route;


Route::get('/language/{locale}', function ($locale) {
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
    $achievements = Achievement::where('is_published', true)
        ->latest()
        ->get();

    return view('/achievements', [
        'page' => 'achievements',
        'achievements' => $achievements,
    ]);
});

Route::get('/contact', function () {
    return view('/contact', ['page' => 'contact']);
});

Route::get('/services/webdev', function () {
    return view('/services/webdev', ['page' => 'services']);
});


/*
|--------------------------------------------------------------------------
| Admin auth routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin protected routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('realisations', AchievementController::class)
            ->except(['show']);

        Route::patch('/messages/{message}/mark-seen', [MessageController::class, 'markSeen'])
            ->name('messages.markSeen');

        Route::patch('/messages/{message}/mark-waiting', [MessageController::class, 'markWaiting'])
            ->name('messages.markWaiting');

        Route::get('/messages/waiting', [MessageController::class, 'waiting'])
            ->name('messages.waiting');

        Route::get('/messages/seen', [MessageController::class, 'seen'])
            ->name('messages.seen');

        Route::get('/messages/{message}/edit', [MessageController::class, 'edit'])
            ->name('messages.edit');

        Route::put('/messages/{message}', [MessageController::class, 'update'])
            ->name('messages.update');

        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
            ->name('messages.destroy');
    });



Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

