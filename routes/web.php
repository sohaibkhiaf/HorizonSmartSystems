<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Models\Achievement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Language Switcher /////////////////

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language-switcher');


// Public Routes //////////////

Route::get('/', function () {

    $achievements = Achievement::where('is_published', true)
        ->latest()
        ->limit(10)
        ->get();

    return view('/home', [
        'page' => 'home', 
        'achievements' => $achievements, 
    ]);
});


// Services /////////////////

Route::get('/services/webdev', function () {
    return view('/services/webdev', ['page' => 'services']);
});

Route::get('/services/appdev', function () {
    return view('/services/appdev', ['page' => 'services']);
});

Route::get('/services/erp', function () {
    return view('/services/erp', ['page' => 'services']);
});

Route::get('/services/systems', function () {
    return view('/services/systems', ['page' => 'services']);
});

Route::get('/services/support', function () {
    return view('/services/support', ['page' => 'services']);
});

// Store /////////////////////////
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/{product}/order', [StoreController::class, 'order'])->name('store.order');
Route::post('/store/{product}/confirm', [StoreController::class, 'confirm'])->name('store.confirm');
Route::get('/store/{request}/confirmed', [StoreController::class, 'confirmed'])->name('store.confirmed');

// Contact /////////////////////

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');


// Admin Auth Routes ////////////////////

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin Protected Routes ////////////////////

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('achievements', AchievementController::class)
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

    Route::resource('users', UserController::class);




    Route::get('/products/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


    Route::get('requests', [RequestController::class, 'index'])
        ->name('requests.index');
    Route::get('requests/create', [RequestController::class, 'create'])
        ->name('requests.create');
    Route::post('requests', [RequestController::class, 'store'])
        ->name('requests.store');
        
    Route::get('requests/{id}', [RequestController::class, 'show'])
        ->name('requests.show');
    Route::post('requests/{id}/availability', [RequestController::class, 'update'])
        ->name('requests.update');

    Route::post('requests/{id}/deliver', [RequestController::class, 'deliverCredentials'])
        ->name('requests.deliver');

    Route::delete('requests/{id}', [RequestController::class, 'destroy'])
        ->name('requests.destroy');
});


