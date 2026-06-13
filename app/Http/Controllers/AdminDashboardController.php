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

            // ESPACE CLIENT
            'customerStats' => [
                'customers_total' => User::where('role', 'CUSTOMER')->count(),

                'orders_total' => CustomerOrder::count(),
                'orders_pending' => CustomerOrder::where('status', 'pending')->count(),
                'orders_processing' => CustomerOrder::where('status', 'processing')->count(),
                'orders_completed' => CustomerOrder::where('status', 'completed')->count(),
                'orders_cancelled' => CustomerOrder::where('status', 'cancelled')->count(),

                'orders_revenue' => CustomerOrder::where('status', 'completed')->sum('price'),

                'services_active' => CustomerService::where('status', 'active')->count(),
                'services_expired' => CustomerService::where('status', 'expired')->count(),
                'services_maintenance' => CustomerService::where('status', 'maintenance')->count(),
                'services_suspended' => CustomerService::where('status', 'suspended')->count(),

                'services_expiring_soon' => CustomerService::where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->whereDate('expires_at', '<=', now()->addDays(7))
                    ->count(),

                'subscriptions_active' => CustomerSubscription::where('status', 'active')->count(),
                'subscriptions_pending' => CustomerSubscription::where('status', 'pending')->count(),
                'subscriptions_expired' => CustomerSubscription::where('status', 'expired')->count(),
                'subscriptions_cancelled' => CustomerSubscription::where('status', 'cancelled')->count(),

                'subscriptions_value' => CustomerSubscription::where('status', 'active')->sum('price'),

                'subscriptions_renewing_soon' => CustomerSubscription::where('status', 'active')
                    ->whereDate('renewal_date', '<=', now()->addDays(7))
                    ->count(),

                'tickets_open' => CustomerTicket::where('status', 'open')->count(),
                'tickets_waiting' => CustomerTicket::where('status', 'waiting')->count(),
                'tickets_answered' => CustomerTicket::where('status', 'answered')->count(),
                'tickets_closed' => CustomerTicket::where('status', 'closed')->count(),
            ],

            // RECENT ACTIVITY
            'latestMessages' => PublicMessage::latest()->take(5)->get(),

            'latestStoreRequests' => StoreRequest::with('storeSubscription')
                ->latest()
                ->take(5)
                ->get(),

            'latestTickets' => CustomerTicket::with('user')
                ->withCount('replies')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
