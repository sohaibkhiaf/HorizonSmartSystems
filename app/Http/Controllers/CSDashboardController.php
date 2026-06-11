<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CSDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->get();
        $services = $user->services()->latest()->get();
        $subscriptions = $user->subscriptions()->latest()->get();

        return view('client.dashboard', [
            'orders_count' => $orders->count(),
            'orders_total' => $orders->sum('price'),
            'services_count' => $services->where('status', 'active')->count(),
            'subscriptions_count' => $subscriptions->where('status', 'active')->count(),
            'open_tickets_count' => $user->tickets()->whereIn('status', ['open', 'waiting'])->count(),
            'recent_orders' => $orders->take(5),
            'recent_services' => $services->take(4),
            'expiring_subscriptions' => $user->subscriptions()
                ->whereDate('renewal_date', '<=', now()->addDays(7))
                ->orderBy('renewal_date')
                ->get()
        ]);
    }
}
