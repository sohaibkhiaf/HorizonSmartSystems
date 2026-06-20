<?php

namespace App\Http\Controllers;

use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index() {
        // query
        $query = StoreSubscription::query();

        // search
        $search = null;
        if (request()->filled('search')) {
            $search = request()->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // category
        $category = null;
        if (request()->filled('category') && request()->category !== 'all') {
            $category = request()->category;
            $query->where('category', $category);
        }

        // paginate
        $subscriptions = $query->latest()->paginate(16)
            ->withQueryString();

        // services
        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        // view
        return view('/store/index', [
            'subscriptions'=> $subscriptions, 
            'search'=> $search,
            'category' => $category, 
            'services' => $services, 
            'hero' => $hero 
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
            'hero' => $hero 
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
            'hero' => $hero 
        ]);
    }

}
