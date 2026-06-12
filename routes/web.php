<?php

use App\Http\Controllers\AdminClientOrderController;
use App\Http\Controllers\AdminClientServiceController;
use App\Http\Controllers\AdminClientSubscriptionController;
use App\Http\Controllers\AdminClientTicketController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CSDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAchievementController;
use App\Http\Controllers\PublicMessageController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\PublicSoftwareController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\StoreSubscriptionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// LANGUAGE SWITCHER ////////////////////////////////////////////////////////////////
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switcher');

// HOME /////////////////////////////////////////////////////////////////////////////
Route::get('/', [HomeController::class, 'home'])
    ->name('home.index');

// STORE /////////////////////////////////////////////////////////////////////////////
Route::get('/store', [StoreController::class, 'index'])
    ->name('store.index');
Route::get('/store/{subscription}/order', [StoreController::class, 'order'])
    ->name('store.order');
Route::post('/store/{subscription}/confirm', [StoreController::class, 'confirm'])
    ->name('store.confirm');
Route::get('/store/{request}/confirmed', [StoreController::class, 'confirmed'])
    ->name('store.confirmed');

// CONTACT US /////////////////////////////////////////////////////////////////////
Route::post('/contact', [PublicMessageController::class, 'store'])
    ->name('contact.message');

// AUTHENTICATION ////////////////////////////////////////////////////////////////
Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ADMIN /////////////////////////////////////////////////////////////////////////
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
    
    // ADMIN DASHBOARD //////////////////////////////////////////////////////////
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->name('dashboard.index');

    // ACHIEVEMENTS /////////////////////////////////////////////////////////////
    Route::get('achievements', [PublicAchievementController::class, 'index'])
        ->name('achievements.index');
    Route::get('achievements/create', [PublicAchievementController::class, 'create'])
        ->name('achievements.create');
    Route::post('achievements', [PublicAchievementController::class, 'store'])
        ->name('achievements.store');
    Route::get('achievements/{achievement}/edit', [PublicAchievementController::class, 'edit'])
        ->name('achievements.edit');
    Route::put('achievements/{achievement}', [PublicAchievementController::class, 'update'])
        ->name('achievements.update');
    Route::delete('achievements/{achievement}', [PublicAchievementController::class, 'destroy'])
        ->name('achievements.destroy');

    // USERS ////////////////////////////////////////////////////////////////////////
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])
        ->name('users.create');
    Route::post('users', [UserController::class, 'store'])
        ->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

    // PUBLIC SERVICES ///////////////////////////////////////////////////////
    Route::get('/public/services/', [PublicServiceController::class, 'index'])
        ->name('public_services.index');
    Route::get('/public/services/create', [PublicServiceController::class, 'create'])
        ->name('public_services.create');
    Route::post('/public/services/store', [PublicServiceController::class, 'store'])
        ->name('public_services.store');
    Route::get('/public/services/{service}/edit', [PublicServiceController::class, 'edit'])
        ->name('public_services.edit');
    Route::put('/public/services/{service}/update', [PublicServiceController::class, 'update'])
        ->name('public_services.update');
    Route::delete('/public/services/{service}/destroy', [PublicServiceController::class, 'destroy'])
        ->name('public_services.destroy');

    // PUBLIC SOFTWARE  //////////////////////////////////////////////////////
    Route::get('/public/software/', [PublicSoftwareController::class, 'index'])
        ->name('public_software.index');
    Route::get('/public/software/create', [PublicSoftwareController::class, 'create'])
        ->name('public_software.create');
    Route::post('/public/software/store', [PublicSoftwareController::class, 'store'])
        ->name('public_software.store');
    Route::get('/public/software/{software}/edit', [PublicSoftwareController::class, 'edit'])
        ->name('public_software.edit');
    Route::put('/public/software/{software}/update', [PublicSoftwareController::class, 'update'])
        ->name('public_software.update');
    Route::delete('/public/software/{software}/destroy', [PublicSoftwareController::class, 'destroy'])
        ->name('public_software.destroy');

    // PUBLIC MESSAGES //////////////////////////////////////////////////////////
    Route::patch('/messages/{message}/seen', [PublicMessageController::class, 'markSeen'])
        ->name('messages.markSeen');
    Route::patch('/messages/{message}/waiting', [PublicMessageController::class, 'markWaiting'])
        ->name('messages.markWaiting');
    Route::get('/messages/waiting', [PublicMessageController::class, 'waiting'])
        ->name('messages.waiting');
    Route::get('/messages/seen', [PublicMessageController::class, 'seen'])
        ->name('messages.seen');
    Route::get('/messages/{message}/edit', [PublicMessageController::class, 'edit'])
        ->name('messages.edit');
    Route::put('/messages/{message}', [PublicMessageController::class, 'update'])
        ->name('messages.update');
    Route::delete('/messages/{message}', [PublicMessageController::class, 'destroy'])
        ->name('messages.destroy');

});

// ADMIN / OPERATOR //////////////////////////////////////////////////////////////
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'operator'])
    ->group(function () {

    // STORE SUBSCRIPTIONS ////////////////////////////////////////////////////
    Route::get('/store/subscriptions/', [StoreSubscriptionController::class, 'index'])
        ->name('store_subs.index');
    Route::get('/store/subscriptions/create', [StoreSubscriptionController::class, 'create'])
        ->name('store_subs.create');
    Route::post('/store/subscriptions/', [StoreSubscriptionController::class, 'store'])
        ->name('store_subs.store');
    Route::get('/store/subscriptions/{product}/edit', [StoreSubscriptionController::class, 'edit'])
        ->name('store_subs.edit');
    Route::put('/store/subscriptions/{product}', [StoreSubscriptionController::class, 'update'])
        ->name('store_subs.update');
    Route::delete('/store/subscriptions/{product}', [StoreSubscriptionController::class, 'destroy'])
        ->name('store_subs.destroy');

    // STORE REQUESTS /////////////////////////////////////////////////////////////
    Route::get('/store/requests', [StoreRequestController::class, 'index'])
        ->name('requests.index');
    Route::get('/store/requests/create', [StoreRequestController::class, 'create'])
        ->name('requests.create');
    Route::post('/store/requests', [StoreRequestController::class, 'store'])
        ->name('requests.store');
    Route::get('/store/requests/{id}', [StoreRequestController::class, 'show'])
        ->name('requests.show');
    Route::post('/store/requests/{id}', [StoreRequestController::class, 'update'])
        ->name('requests.update');
    Route::delete('/store/requests/{id}', [StoreRequestController::class, 'destroy'])
        ->name('requests.destroy');

    // CLIENT ORDERS ///////////////////////////////////////////CONTINUE
    Route::get('/client/orders', [AdminClientOrderController::class, 'index'])
        ->name('client-orders.index');
    Route::get('/client/orders/create', [AdminClientOrderController::class, 'create'])
        ->name('client-orders.create');
    Route::post('/client/orders', [AdminClientOrderController::class, 'store'])
        ->name('client-orders.store');
    Route::get('/client/orders/{clientOrder}/edit', [AdminClientOrderController::class, 'edit'])
        ->name('client-orders.edit');
    Route::put('/client/orders/{clientOrder}', [AdminClientOrderController::class, 'update'])
        ->name('client-orders.update');

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
