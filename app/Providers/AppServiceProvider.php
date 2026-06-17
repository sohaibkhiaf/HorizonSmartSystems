<?php

namespace App\Providers;

use App\Models\CustomerChatMessage;
use App\Models\CustomerDemo;
use App\Models\PublicAchievement;
use App\Models\PublicMessage;
use App\Models\PublicService;
use App\Models\PublicSoftware;
use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use App\Models\User;
use App\Observers\CustomerChatMessageObserver;
use App\Observers\CustomerDemoObserver;
use App\Observers\PublicAchievementObserver;
use App\Observers\PublicMessageObserver;
use App\Observers\PublicServiceObserver;
use App\Observers\PublicSoftwareObserver;
use App\Observers\StoreRequestObserver;
use App\Observers\StoreSubscriptionObserver;
use App\Observers\UserObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // RATE LIMITER //////////////////
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('order', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(2)->by($request->ip());
        });
        
        RateLimiter::for('chat', function (Request $request) {
            return Limit::perMinute(20)->by(auth()->id());
        });

        // OBSERVER /////////////////////
        User::observe(UserObserver::class);

        PublicMessage::observe(PublicMessageObserver::class);
        PublicAchievement::observe(PublicAchievementObserver::class);
        PublicService::observe(PublicServiceObserver::class);
        PublicSoftware::observe(PublicSoftwareObserver::class);

        StoreSubscription::observe(StoreSubscriptionObserver::class);
        StoreRequest::observe(StoreRequestObserver::class);

        CustomerDemo::observe(CustomerDemoObserver::class);
        CustomerChatMessage::observe(CustomerChatMessageObserver::class);
    }
}
