<?php

namespace App\Http\Controllers;

use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use App\Support\LocalizedUrl;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display the public store.
     */
    public function index(): View
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

        /*
         * Search.
         */
        $search = trim(
            (string) request('search', '')
        );

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                );
            });
        } else {
            $search = null;
        }

        /*
         * Category.
         */
        $category = request(
            'category',
            'all'
        );

        if (
            !in_array(
                $category,
                array_merge(
                    ['all'],
                    $allowedCategories
                ),
                true
            )
        ) {
            $category = 'all';
        }

        if ($category !== 'all') {
            $query->where(
                'category',
                $category
            );
        }

        /*
         * Products.
         */
        $subscriptions = $query
            ->orderByDesc('priority')
            ->latest()
            ->paginate(16)
            ->withQueryString();

        /*
         * Statistics.
         */
        $totalSubscriptions =
            StoreSubscription::query()
                ->where('is_published', true)
                ->count();

        $discountedCount =
            StoreSubscription::query()
                ->where('is_published', true)
                ->whereNotNull('discount')
                ->where('discount', '>', 0)
                ->count();

        $categoryCounts =
            StoreSubscription::query()
                ->where('is_published', true)
                ->select('category')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category');

        /*
         * Bestsellers are hidden while searching.
         */
        $bestSellers = collect();

        if (empty($search)) {
            $bestSellerIds =
                StoreRequest::query()
                    ->select('store_subscription_id')
                    ->selectRaw(
                        'SUM(quantity) as total_quantity'
                    )
                    ->whereNotNull(
                        'store_subscription_id'
                    )
                    ->groupBy(
                        'store_subscription_id'
                    )
                    ->orderByDesc(
                        'total_quantity'
                    )
                    ->limit(10)
                    ->pluck(
                        'store_subscription_id'
                    )
                    ->filter()
                    ->values();

            $bestSellers =
                StoreSubscription::query()
                    ->where('is_published', true)
                    ->whereIn(
                        'id',
                        $bestSellerIds
                    )
                    ->get()
                    ->sortBy(
                        fn ($item) =>
                            $bestSellerIds->search(
                                $item->id
                            )
                    )
                    ->values();

            /*
             * Fallback to discounted products.
             */
            if ($bestSellers->count() < 10) {
                $extraDiscounted =
                    StoreSubscription::query()
                        ->where(
                            'is_published',
                            true
                        )
                        ->whereNotIn(
                            'id',
                            $bestSellers->pluck('id')
                        )
                        ->whereNotNull('discount')
                        ->where(
                            'discount',
                            '>',
                            0
                        )
                        ->orderByDesc('priority')
                        ->latest()
                        ->limit(
                            10 - $bestSellers->count()
                        )
                        ->get();

                $bestSellers = $bestSellers
                    ->concat($extraDiscounted)
                    ->values();
            }

            /*
             * Final fallback to latest products.
             */
            if ($bestSellers->count() < 10) {
                $extraLatest =
                    StoreSubscription::query()
                        ->where(
                            'is_published',
                            true
                        )
                        ->whereNotIn(
                            'id',
                            $bestSellers->pluck('id')
                        )
                        ->orderByDesc('priority')
                        ->latest()
                        ->limit(
                            10 - $bestSellers->count()
                        )
                        ->get();

                $bestSellers = $bestSellers
                    ->concat($extraLatest)
                    ->values();
            }
        }

        $services = PublicService::query()
            ->where('is_published', true)
            ->get();

        $hero = PublicHero::findOrFail(1);

        return view('store.index', [
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

    /**
     * Display the order form.
     */
    public function order(
        StoreSubscription $subscription
    ): View {
        abort_if(
            !$subscription->is_published,
            404
        );

        $services = PublicService::query()
            ->where('is_published', true)
            ->get();

        $hero = PublicHero::findOrFail(1);

        return view('store.order', [
            'subscription' => $subscription,
            'services' => $services,
            'hero' => $hero,
            'seo' => Seo::privatePage(
                __('order.title')
            ),
        ]);
    }

    /**
     * Store the order.
     */
    public function confirm(
        Request $request,
        StoreSubscription $subscription
    ): RedirectResponse {
        abort_if(
            !$subscription->is_published,
            404
        );

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:200',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $hasDiscount =
            $subscription->discount !== null
            && $subscription->discount > 0;

        $unitPrice = $hasDiscount
            ? $subscription->discount
            : $subscription->price;

        $storeRequest = StoreRequest::create([
            'store_subscription_id' =>
                $subscription->id,

            'customer_full_name' =>
                $data['full_name'],

            'customer_contact' =>
                $data['phone'],

            'customer_email' =>
                $data['email'] ?? null,

            'customer_address' =>
                $data['address'] ?? null,

            'quantity' =>
                $data['quantity'],

            'unit_price' =>
                $unitPrice,
        ]);

        return redirect()->to(
            LocalizedUrl::route(
                'store.confirmed',
                [
                    'request' => $storeRequest,
                ],
                app()->getLocale(),
                [],
                false
            )
        );
    }

    /**
     * Display order confirmation.
     */
    public function confirmed(
        StoreRequest $request
    ): View {
        $services = PublicService::query()
            ->where('is_published', true)
            ->get();

        $hero = PublicHero::findOrFail(1);

        return view('store.confirmed', [
            'request' => $request,
            'services' => $services,
            'hero' => $hero,
            'seo' => Seo::privatePage(
                __('confirmed.title')
            ),
        ]);
    }
}

