<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        return view('admin.products.create');
    }

    public function store(Request $req) {
        Product::create([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price, 
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('admin.products.index');
    }

    public function edit($id) {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $req, $id) {
        $product = Product::findOrFail($id);

        $product->update([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('admin.products.index');
    }

    public function destroy($id) {
        Product::destroy($id);
        return back();
    }   
}
