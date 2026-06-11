<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AdminClientOrderController;
use App\Http\Controllers\AdminClientServiceController;
use App\Http\Controllers\AdminClientSubscriptionController;
use App\Http\Controllers\AdminClientTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CSAuthController;
use App\Http\Controllers\CSDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
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
// Home
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

Route::get('/webdev', function () {
    return view('/services/webdev', ['page' => 'services']);
});

Route::get('/appdev', function () {
    return view('/services/appdev', ['page' => 'services']);
});

Route::get('/erp', function () {
    return view('/services/erp', ['page' => 'services']);
});

Route::get('/systems', function () {
    return view('/services/systems', ['page' => 'services']);
});

Route::get('/support', function () {
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
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Achievements
    Route::resource('achievements', AchievementController::class)
        ->except(['show']);

    // Messages
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

    // Products
    Route::get('/products/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Requests
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

    Route::get('client-orders', [AdminClientOrderController::class, 'index'])->name('client-orders.index');
    Route::get('client-orders/create', [AdminClientOrderController::class, 'create'])->name('client-orders.create');
    Route::post('client-orders', [AdminClientOrderController::class, 'store'])->name('client-orders.store');
    Route::get('client-orders/{clientOrder}/edit', [AdminClientOrderController::class, 'edit'])->name('client-orders.edit');
    Route::put('client-orders/{clientOrder}', [AdminClientOrderController::class, 'update'])->name('client-orders.update');

    Route::get('client-services', [AdminClientServiceController::class, 'index'])->name('client-services.index');
    Route::get('client-services/create', [AdminClientServiceController::class, 'create'])->name('client-services.create');
    Route::post('client-services', [AdminClientServiceController::class, 'store'])->name('client-services.store');
    Route::get('client-services/{clientService}/edit', [AdminClientServiceController::class, 'edit'])->name('client-services.edit');
    Route::put('client-services/{clientService}', [AdminClientServiceController::class, 'update'])->name('client-services.update');

    Route::get('client-subscriptions', [AdminClientSubscriptionController::class, 'index'])->name('client-subscriptions.index');
    Route::get('client-subscriptions/create', [AdminClientSubscriptionController::class, 'create'])->name('client-subscriptions.create');
    Route::post('client-subscriptions', [AdminClientSubscriptionController::class, 'store'])->name('client-subscriptions.store');
    Route::get('client-subscriptions/{clientSubscription}/edit', [AdminClientSubscriptionController::class, 'edit'])->name('client-subscriptions.edit');
    Route::put('client-subscriptions/{clientSubscription}', [AdminClientSubscriptionController::class, 'update'])->name('client-subscriptions.update');

    Route::get('client-tickets', [AdminClientTicketController::class, 'index'])->name('client-tickets.index');
    Route::get('client-tickets/{ticket}', [AdminClientTicketController::class, 'show'])->name('client-tickets.show');
    Route::post('client-tickets/{ticket}/reply', [AdminClientTicketController::class, 'reply'])->name('client-tickets.reply');
});

// Client Space ////////////////////

// Client Auth Routes 

Route::get('/client/login', [CSAuthController::class, 'loginPage'])->name('client.login.page');
Route::post('/client/login', [CSAuthController::class, 'login'])->name('client.login.submit');
Route::post('/client/logout', [CSAuthController::class, 'logout'])->name('client.logout');

Route::middleware(['auth' , 'client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {

    Route::get('/dashboard', [CSDashboardController::class, 'index'])->name('dashboard');

    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    Route::get('services', [ServiceController::class, 'index'])->name('services.index');

    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('tickets/{id}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
});
