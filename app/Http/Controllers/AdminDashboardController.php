<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerSubscription;
use App\Models\CustomerTicket;
use App\Models\PublicAchievement;
use App\Models\PublicMessage;
use App\Models\StoreRequest;
use App\Models\CustomerService;
use App\Models\PublicService;
use App\Models\PublicSoftware;
use App\Models\StoreSubscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            // SITE PUBLIC
            'publicStats' => [
                'messages_waiting' => PublicMessage::where('is_seen', false)->count(),
                'messages_seen' => PublicMessage::where('is_seen', true)->count(),

                'achievements_total' => PublicAchievement::count(),
                'achievements_published' => PublicAchievement::where('is_published', true)->count(),

                'services_total' => PublicService::count(),
                'services_published' => PublicService::where('is_published', true)->count(),

                'software_total' => PublicSoftware::count(),
                'software_published' => PublicSoftware::where('is_published', true)->count(),
            ],

            // BOUTIQUE
            'storeStats' => [
                'products_total' => StoreSubscription::count(),

                'requests_total' => StoreRequest::count(),
                'requests_pending' => StoreRequest::where('status', 'pending')->count(),
                'requests_processing' => StoreRequest::where('status', 'processing')->count(),
                'requests_available' => StoreRequest::where('status', 'available')->count(),
                'requests_postponed' => StoreRequest::where('status', 'postponed')->count(),
                'requests_paid' => StoreRequest::where('status', 'paid')->count(),
                'requests_delivered' => StoreRequest::where('status', 'delivered')->count(),
                'requests_cancelled' => StoreRequest::where('status', 'cancelled')->count(),

                'revenue_paid' => StoreRequest::join('store_subscriptions', 'store_requests.store_subscription_id', '=', 'store_subscriptions.id')
                    ->whereIn('store_requests.status', ['paid', 'delivered'])
                    ->sum(DB::raw('CAST(store_requests.quantity AS UNSIGNED) * store_subscriptions.price')),
            ],

            // RECENT ACTIVITY
            'latestMessages' => PublicMessage::latest()->take(5)->get(),

            'latestStoreRequests' => StoreRequest::with('storeSubscription')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
