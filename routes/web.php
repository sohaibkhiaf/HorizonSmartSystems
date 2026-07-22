<?php


use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PublicAchievementController;
use App\Http\Controllers\PublicHeroController;
use App\Http\Controllers\PublicMessageController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\PublicSoftwareController;
use App\Http\Controllers\SoftwareDemoController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\StoreSubscriptionController;
use App\Http\Controllers\UserController;
use App\Support\LocalizedUrl;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', SitemapController::class)
    ->name('sitemap');

/*
|--------------------------------------------------------------------------
| Redirect the root URL to the preferred language
|--------------------------------------------------------------------------
|
| Examples:
| /  -> /ar
| /  -> /fr
| /  -> /en
|
*/

Route::get('/', function () {
    return redirect()->to(
        LocalizedUrl::route(
            'home.index',
            [],
            app()->getLocale(),
            [],
            false
        )
    );
})->name('root');

/*
|--------------------------------------------------------------------------
| Language switcher
|--------------------------------------------------------------------------
*/

Route::get(
    '/language/{locale}',
    [LanguageController::class, 'switch']
)
    ->whereIn('locale', ['ar', 'fr', 'en'])
    ->name('language.switcher');

/*
|--------------------------------------------------------------------------
| Localized route segments
|--------------------------------------------------------------------------
*/

$localizedRoutes = [
    'ar' => [
        'services' => 'الخدمات',
        'software' => 'البرامج',
        'store' => 'المتجر',
        'order' => 'طلب',
        'confirm' => 'تأكيد',
        'confirmed' => 'تم-التأكيد',
        'contact' => 'اتصل-بنا',
        'login' => 'تسجيل-الدخول',
        'logout' => 'تسجيل-الخروج',
    ],

    'fr' => [
        'services' => 'services',
        'software' => 'logiciels',
        'store' => 'boutique',
        'order' => 'commande',
        'confirm' => 'confirmer',
        'confirmed' => 'commande-confirmee',
        'contact' => 'contact',
        'login' => 'connexion',
        'logout' => 'deconnexion',
    ],

    'en' => [
        'services' => 'services',
        'software' => 'software',
        'store' => 'store',
        'order' => 'order',
        'confirm' => 'confirm',
        'confirmed' => 'confirmed',
        'contact' => 'contact',
        'login' => 'login',
        'logout' => 'logout',
    ],
];

/*
|--------------------------------------------------------------------------
| Register localized public routes
|--------------------------------------------------------------------------
*/

foreach ($localizedRoutes as $locale => $segments) {
    Route::prefix($locale)
        ->name($locale . '.')
        ->group(function () use ($locale, $segments) {

            /*
            |--------------------------------------------------------------------------
            | Home
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/',
                [HomeController::class, 'home']
            )->name('home.index');

            /*
            |--------------------------------------------------------------------------
            | Services
            |--------------------------------------------------------------------------
            |
            | Arabic:  {service:ar_slug}
            | French:  {service:fr_slug}
            | English: {service:en_slug}
            |
            */

            Route::get(
                '/' . $segments['services']
                . '/{service:' . $locale . '_slug}',
                [HomeController::class, 'show']
            )->name('services.show');

            /*
            |--------------------------------------------------------------------------
            | Software
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/' . $segments['software']
                . '/{software:' . $locale . '_slug}',
                [HomeController::class, 'software']
            )->name('software.show');

            /*
            |--------------------------------------------------------------------------
            | Store
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/' . $segments['store'],
                [StoreController::class, 'index']
            )->name('store.index');

            Route::get(
                '/' . $segments['store']
                . '/{subscription:' . $locale . '_slug}'
                . '/' . $segments['order'],
                [StoreController::class, 'order']
            )->name('store.order');

            Route::post(
                '/' . $segments['store']
                . '/{subscription:' . $locale . '_slug}'
                . '/' . $segments['confirm'],
                [StoreController::class, 'confirm']
            )
                ->middleware('throttle:order')
                ->name('store.confirm');

            Route::get(
                '/' . $segments['store']
                . '/{request}'
                . '/' . $segments['confirmed'],
                [StoreController::class, 'confirmed']
            )->name('store.confirmed');

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            Route::post(
                '/' . $segments['contact'],
                [PublicMessageController::class, 'store']
            )
                ->middleware('throttle:contact')
                ->name('contact.message');

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/' . $segments['login'],
                [AuthController::class, 'loginPage']
            )->name('login.page');

            Route::post(
                '/' . $segments['login'],
                [AuthController::class, 'login']
            )
                ->middleware('throttle:login')
                ->name('login.submit');

            Route::post(
                '/' . $segments['logout'],
                [AuthController::class, 'logout']
            )->name('logout');
        });
}


// ADMIN //////////////////////////////////////////////////////////////////////
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

    // ACTIVITY LOGS ///////////////////////////////////////////////////////////
    Route::get('/logs', [ActivityLogController::class, 'index'])
        ->name('logs.index');
    Route::delete('/logs', [ActivityLogController::class, 'delete'])
        ->name('logs.destroy');
});


// ADMIN / OPERATOR //////////////////////////////////////////////////////////////
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin_or_operator'])
    ->group(function () {

    // ADMIN DASHBOARD //////////////////////////////////////////////////////////
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->name('dashboard.index');

    // USERS ////////////////////////////////////////////////////////////////////////
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])
        ->name('users.create');
    Route::post('users', [UserController::class, 'store'])
        ->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

    // CHATS ///////////////////////////////////////////////////////////////////////////
    Route::get('/chats', [AdminChatController::class, 'index'])
        ->name('chats.index');
    Route::post('/chats/messages', [AdminChatController::class, 'sendMessage'])
        ->name('chats.messages.store');
    Route::post('/chats/groups', [AdminChatController::class, 'storeGroup'])
        ->name('chats.groups.store');
    Route::put('/chats/groups/{customerGroup}', [AdminChatController::class, 'updateGroup'])
        ->name('chats.groups.update');
    Route::delete('/chats/groups/{customerGroup}', [AdminChatController::class, 'destroyGroup'])
        ->name('chats.groups.destroy');

    //  PUBLIC ACHIEVEMENTS /////////////////////////////////////////////////////////////
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
        ->name('public_messages.markSeen');
    Route::patch('/messages/{message}/waiting', [PublicMessageController::class, 'markWaiting'])
        ->name('public_messages.markWaiting');
    Route::get('/messages/waiting', [PublicMessageController::class, 'waiting'])
        ->name('public_messages.waiting');
    Route::get('/messages/seen', [PublicMessageController::class, 'seen'])
        ->name('public_messages.seen');
    Route::get('/messages/{message}/edit', [PublicMessageController::class, 'edit'])
        ->name('public_messages.edit');
    Route::put('/messages/{message}', [PublicMessageController::class, 'update'])
        ->name('public_messages.update');
    Route::delete('/messages/{message}', [PublicMessageController::class, 'destroy'])
        ->name('public_messages.destroy');

    // STORE SUBSCRIPTIONS ////////////////////////////////////////////////////
    Route::get('/store/subscriptions/', [StoreSubscriptionController::class, 'index'])
        ->name('store_subs.index');
    Route::get('/store/subscriptions/create', [StoreSubscriptionController::class, 'create'])
        ->name('store_subs.create');
    Route::post('/store/subscriptions/', [StoreSubscriptionController::class, 'store'])
        ->name('store_subs.store');
    Route::get('/store/subscriptions/{subscription}/edit', [StoreSubscriptionController::class, 'edit'])
        ->name('store_subs.edit');
    Route::put('/store/subscriptions/{subscription}', [StoreSubscriptionController::class, 'update'])
        ->name('store_subs.update');
    Route::delete('/store/subscriptions/{subscription}', [StoreSubscriptionController::class, 'destroy'])
        ->name('store_subs.destroy');

    // STORE REQUESTS /////////////////////////////////////////////////////////////
    Route::get('/store/requests', [StoreRequestController::class, 'index'])
        ->name('store_requests.index');
    Route::get('/store/requests/create', [StoreRequestController::class, 'create'])
        ->name('store_requests.create');
    Route::post('/store/requests', [StoreRequestController::class, 'store'])
        ->name('store_requests.store');
    Route::get('/store/requests/{id}', [StoreRequestController::class, 'show'])
        ->name('store_requests.show');
    Route::post('/store/requests/{id}', [StoreRequestController::class, 'update'])
        ->name('store_requests.update');
    Route::delete('/store/requests/{id}', [StoreRequestController::class, 'destroy'])
        ->name('store_requests.destroy');
    Route::put('/store/requests/{id}/info', [StoreRequestController::class, 'updateInfo'])
        ->name('store_requests.updateInfo');

    // PUBLIC HERO  ///////////////////////////////////////////////////////////
    Route::get('/hero/{hero}', [PublicHeroController::class, 'show'])
        ->name('public_hero.show');
    Route::put('/hero/{hero}', [PublicHeroController::class, 'update'])
        ->name('public_hero.update');

    // DEMO //////////////////////////////////////////////////////////////////////
    Route::resource('demos', SoftwareDemoController::class)
            ->except(['show'])
            ->names('software_demos');

});

// CUSTOMER PORTAL //////////////////////////////////////////////////////////////////////////
Route::middleware(['auth' , 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

    // PORTAL  ////////////////////////////////////////////////////////////////
    Route::get('/portal', [CustomerPortalController::class, 'index'])
        ->name('portal.index');
    Route::put('/portal/profile', [CustomerPortalController::class, 'updateProfile'])
        ->name('portal_profile.update');
    Route::post('/portal/chat', [CustomerPortalController::class, 'sendMessage'])
        ->middleware('throttle:chat')
        ->name('portal_chat.send');
});








