<?php


use App\Http\Controllers\AdminCustomerPortalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PublicAchievementController;
use App\Http\Controllers\PublicMessageController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\PublicSoftwareController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\StoreSubscriptionController;
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


    Route::get('/customer-portals', [AdminCustomerPortalController::class, 'index'])
        ->name('customer_portals.index');

    Route::get('/customer-portals/{user}', [AdminCustomerPortalController::class, 'show'])
        ->name('customer_portals.show');

    Route::post('/customer-portals/{user}/demos', [AdminCustomerPortalController::class, 'storeDemo'])
        ->name('customer_portals.demos.store');

    Route::put('/customer-portals/demos/{demo}', [AdminCustomerPortalController::class, 'updateDemo'])
        ->name('customer_portals.demos.update');

    Route::delete('/customer-portals/demos/{demo}', [AdminCustomerPortalController::class, 'destroyDemo'])
        ->name('customer_portals.demos.destroy');

    Route::post('/customer-portals/{user}/messages', [AdminCustomerPortalController::class, 'sendMessage'])
        ->name('customer_portals.messages.send');

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
    Route::put('/store/requests/{id}/info', [StoreRequestController::class, 'updateInfo'])
        ->name('requests.updateInfo');
});


// CUSTOMER PORTAL //////////////////////////////////////////////////////////////////////////
Route::middleware(['auth' , 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

    Route::get('/portal', [CustomerPortalController::class, 'index'])
        ->name('portal.index');

    Route::put('/portal/contact', [CustomerPortalController::class, 'updateContact'])
        ->name('portal.contact.update');

    Route::post('/portal/chat', [CustomerPortalController::class, 'sendMessage'])
        ->name('portal.chat.send');
});
