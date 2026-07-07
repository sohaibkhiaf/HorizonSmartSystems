<?php

namespace App\Http\Controllers;

use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use App\Support\Seo;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $allowedCategories = [
            'games',
            'gift_cards',
            'software',
            'subscriptions',
            'in_game_currency',
            'other',
        ];

        $query = StoreSubscription::query()
            ->where('is_published', true);

        // Search
        $search = trim((string) request('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        } else {
            $search = null;
        }

        // Category
        $category = request('category', 'all');

        if (!in_array($category, array_merge(['all'], $allowedCategories), true)) {
            $category = 'all';
        }

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        // Products
        $subscriptions = $query
            ->orderByDesc('priority')
            ->latest()
            ->paginate(16)
            ->withQueryString();

        // Details / Counts
        $totalSubscriptions = StoreSubscription::where('is_published', true)->count();

        $discountedCount = StoreSubscription::where('is_published', true)
            ->whereNotNull('discount')
            ->where('discount', '>', 0)
            ->count();

        $categoryCounts = StoreSubscription::where('is_published', true)
            ->select('category')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        // Bestsellers: hidden when search is active
        $bestSellers = collect();

        if (empty($search)) {
            $bestSellerIds = StoreRequest::select('store_subscription_id')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->whereNotNull('store_subscription_id')
                ->groupBy('store_subscription_id')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->pluck('store_subscription_id')
                ->filter()
                ->values();

            $bestSellers = StoreSubscription::where('is_published', true)
                ->whereIn('id', $bestSellerIds)
                ->get()
                ->sortBy(fn ($item) => $bestSellerIds->search($item->id))
                ->values();

            // Fallback: discounted products
            if ($bestSellers->count() < 10) {
                $extraDiscounted = StoreSubscription::where('is_published', true)
                    ->whereNotIn('id', $bestSellers->pluck('id'))
                    ->whereNotNull('discount')
                    ->where('discount', '>', 0)
                    ->orderByDesc('priority')
                    ->latest()
                    ->limit(10 - $bestSellers->count())
                    ->get();

                $bestSellers = $bestSellers->concat($extraDiscounted)->values();
            }

            // Final fallback: latest products
            if ($bestSellers->count() < 10) {
                $extraLatest = StoreSubscription::where('is_published', true)
                    ->whereNotIn('id', $bestSellers->pluck('id'))
                    ->orderByDesc('priority')
                    ->latest()
                    ->limit(10 - $bestSellers->count())
                    ->get();

                $bestSellers = $bestSellers->concat($extraLatest)->values();
            }
        }

        // Services
        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        return view('/store/index', [
            'subscriptions' => $subscriptions,
            'search' => $search,
            'category' => $category,
            'services' => $services,
            'hero' => $hero,
            'bestSellers' => $bestSellers,
            'categoryCounts' => $categoryCounts,
            'totalSubscriptions' => $totalSubscriptions,
            'discountedCount' => $discountedCount,
            'seo' => Seo::storeIndex(),
        ]);
    }

    public function order(StoreSubscription $subscription) {

        // services
        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        // view
        return view('/store/order', [
            'subscription'=> $subscription, 
            'services' => $services, 
            'hero' => $hero,
            'seo' => Seo::privatePage(__('order.title')),
        ]);
    }

    public function confirm(Request $request) {

        // validation
        $data = $request->validate([
            'subscription_id' => ['required', 'integer'],
            'full_name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'quantity' => ['required', 'integer'], 
        ]);

        $subscription = StoreSubscription::findOrFail($data['subscription_id']);
        $hasDiscount = !empty($subscription->discount) && $subscription->discount > 0;
        $unitPrice =  $hasDiscount ? $subscription->discount : $subscription->price;

        // create
        $request = StoreRequest::create([
            'store_subscription_id'=> $data['subscription_id'], 
            'customer_full_name'=> $data['full_name'], 
            'customer_contact'=> $data['phone'], 
            'customer_email'=> $data['email'], 
            'customer_address'=> $data['address'], 
            'quantity' => $data['quantity'],
            'unit_price' => $unitPrice, 
        ]);

        // reditect
        return redirect()->route('store.confirmed', [
            'request' => $request, 
        ]);
    }

    public function confirmed(StoreRequest $request) {

        // services
        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        // view
        return view('/store/confirmed', [
            'request'=> $request, 
            'services' => $services, 
            'hero' => $hero,
            'seo' => Seo::privatePage(__('confirmed.title')),
        ]);
    }

}
