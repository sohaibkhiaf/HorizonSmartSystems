<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index() {
        // query
        $query = Product::query();

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

    public function order(Product $product) {
        // view
        return view('/store/order', [
            'product'=> $product, 
            'page' => 'store', 
        ]);
    }

    public function confirm(Request $request, Product $product) {
        // validation
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'full_name' => ['required', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'quantity' => ['required', 'integer'], 
        ]);

        // create
        $request = \App\Models\Request::create([
            'product_id'=> $data['product_id'], 
            'client_name'=> $data['full_name'], 
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

    public function confirmed(\App\Models\Request $request) {

        // view
        return view('/store/confirmed', [
            'request'=> $request, 
            'page' => 'services', 
        ]);
    }

}
