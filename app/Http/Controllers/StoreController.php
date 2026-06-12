<?php

namespace App\Http\Controllers;

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
        $products = $query->latest()->paginate(12);

        // view
        return view('/store/index', [
            'products'=> $products, 
            'search'=> $search,
            'category' => $category, 
            'page' => 'store', 
        ]);
    }

    public function order(StoreSubscription $product) {
        // view
        return view('/store/order', [
            'product'=> $product, 
            'page' => 'store', 
        ]);
    }

    public function confirm(Request $request, StoreSubscription $storeSubscription) {
        // validation
        $data = $request->validate([
            'store_subscription_id' => ['required', 'integer'],
            'full_name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'quantity' => ['required', 'integer'], 
        ]);

        // create
        $request = StoreRequest::create([
            'store_subscription_id'=> $data['subscription_id'], 
            'client_full_name'=> $data['full_name'], 
            'client_contact'=> $data['phone'], 
            'client_email'=> $data['email'], 
            'client_address'=> $data['address'], 
            'quantity' => $data['quantity'],
        ]);

        // reditect
        return redirect()->route('store.confirmed', [
            'request' => $request, 
        ]);
    }

    public function confirmed(StoreRequest $request) {

        // view
        return view('/store/confirmed', [
            'request'=> $request, 
            'page' => 'services', 
        ]);
    }

}
