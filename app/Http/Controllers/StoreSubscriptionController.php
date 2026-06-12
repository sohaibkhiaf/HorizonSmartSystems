<?php

namespace App\Http\Controllers;

use App\Models\StoreSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSubscriptionController extends Controller
{
    public function index() {
        $products = StoreSubscription::all();
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        return view('admin.products.create');
    }

    public function store(Request $req) {

        $image_url = null;
        if ($req->hasFile('image')) {
            $image_url = $req->file('image')->store('products', 'public');
        }

        StoreSubscription::create([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price, 
            'image' => $image_url,
            'type' => $req->type,
            'category' => $req->category,
        ]);

        return redirect()->route('admin.products.index');
    }

    public function edit($id) {
        $product = StoreSubscription::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $req, $id) {
        $product = StoreSubscription::findOrFail($id);

        $image_url = $product->image;
        if ($req->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image_url = $req->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price,
            'image' => $image_url,
            'type' => $req->type, 
            'category' => $req->category,
        ]);

        return redirect()->route('admin.products.index');
    }

    public function destroy($id) {
        StoreSubscription::destroy($id);
        return back();
    }   
}
