<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;


class RequestController extends Controller
{
    public function index() {
        $status = request()->query('status');

        $allowedStatuses = [
            'pending',
            'processing',
            'available',
            'paid',
            'delivered',
            'postponed',
            'cancelled',
        ];

        $requests = \App\Models\Request::query()
            ->when(in_array($status, $allowedStatuses), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.requests.index', compact('requests', 'status'));
    }

    public function show($id) {
        $request = \App\Models\Request::with('product')->findOrFail($id);
        return view('admin.requests.show', compact('request'));
    }


    public function create() {
        $products = Product::all();
        return view('admin.requests.create', compact('products'));
    }

    public function store(Request $req) {
        \App\Models\Request::create([
            'product_id' => $req->product_id,
            'operator_id' => auth()->id(),
            'client_name' => $req->client_name,
            'client_contact' => $req->client_contact,
            'client_email' => $req->client_email, 
            'client_address' => $req->client_address, 
            'quantity' => $req->quantity, 
        ]);

        return redirect()->route('admin.requests.index')
            ->with('success', 'Demande créée avec succès !');
    }
    
    public function update(Request $req, $id) {

        $req->validate([
            'admin_note' => 'nullable|string|max:512',
        ]);
        
        $request = \App\Models\Request::findOrFail($id);

        $request->update([
            'status' => $req->status,
            'admin_note' => $req->admin_note,
        ]);

        return back();
    }



    public function destroy($id) {
        \App\Models\Request::find($id)->delete();
        return back();
    }
}
