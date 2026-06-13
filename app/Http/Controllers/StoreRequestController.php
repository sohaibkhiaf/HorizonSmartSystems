<?php

namespace App\Http\Controllers;

use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use Illuminate\Http\Request;

class StoreRequestController extends Controller
{
    public function index() {
        // query
        $status = request()->query('status');

        // allowed
        $allowedStatuses = [
            'pending',
            'processing',
            'available',
            'paid',
            'delivered',
            'postponed',
            'cancelled',
        ];

        // fetch
        $requests = StoreRequest::query()
            ->when(in_array($status, $allowedStatuses), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        // view
        return view('admin/requests/index', compact('requests', 'status'));
    }

    public function show($id) {
        // find
        $request = StoreRequest::with('storeSubscription')->findOrFail($id);

        // view
        return view('admin/requests/show', compact('request'));
    }

    public function create() {
        // fetch
        $subscriptions = StoreSubscription::all();

        // view
        return view('admin/requests/create', compact('subscriptions'));
    }

    public function store(Request $req) {
        // validation
        $req->validate([
            'subscription_id'     => 'required|exists:store_subscriptions,id',
            'customer_full_name'    => 'required|string|max:255',
            'customer_contact'      => 'required|string|max:50',
            'customer_email'        => 'nullable|email|max:255',
            'customer_address'      => 'nullable|string|max:500',
            'quantity'            => 'required|integer|min:1',
        ]);

        // create
        StoreRequest::create([
            'store_subscription_id' => $req->subscription_id,
            'customer_full_name' => $req->customer_full_name,
            'customer_contact' => $req->customer_contact,
            'customer_email' => $req->customer_email, 
            'customer_address' => $req->customer_address, 
            'quantity' => $req->quantity, 
        ]);

        // redirect
        return redirect()->route('admin.requests.index')
            ->with('success', 'Demande créée avec succès !');
    }
    
    public function update(Request $req, $id) {

        // validate
        $req->validate([
            'admin_note' => 'nullable|string|max:512',
        ]);
        
        // find
        $request = StoreRequest::findOrFail($id);

        // update
        $request->update([
            'status' => $req->status,
            'admin_note' => $req->admin_note,
        ]);

        // back
        return back();
    }


    public function destroy($id) {
        // delete
        StoreRequest::find($id)->delete();

        // back
        return back();
    }
}
