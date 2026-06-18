<?php

namespace App\Http\Controllers;

use App\Models\StoreSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = StoreSubscription::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.store_subscriptions.index', compact('subscriptions'));
    }
    
    public function create() {

        // view
        return view('admin/store_subscriptions/create');
    }

    public function store(Request $req) {

        // image
        $image_url = null;
        if ($req->hasFile('image')) {
            $image_url = $req->file('image')->store('subscriptions', 'public');
        }

        // create
        StoreSubscription::create([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price, 
            'image' => $image_url,
            'type' => $req->type,
            'category' => $req->category,
        ]);

        // redirect
        return redirect()->route('admin.store_subs.index')
            ->with('success', 'L’abonnement a été créé avec succès.');;
    }

    public function edit($id) {
        // find
        $subscription = StoreSubscription::findOrFail($id);

        // view
        return view('admin/store_subscriptions/edit', compact('subscription'));
    }

    public function update(Request $req, $id) {
        // find
        $subscription = StoreSubscription::findOrFail($id);

        // image
        $image_url = $subscription->image;
        if ($req->hasFile('image')) {
            if ($subscription->image) {
                Storage::disk('public')->delete($subscription->image);
            }

            $image_url = $req->file('image')->store('subscriptions', 'public');
        }

        // update
        $subscription->update([
            'name' => $req->name,
            'description' => $req->description,
            'price' => $req->price,
            'image' => $image_url,
            'type' => $req->type, 
            'category' => $req->category,
        ]);

        // redirect
        return redirect()->route('admin.store_subs.index')
            ->with('success', 'L’abonnement a été mis à jour avec succès.');
    }

    public function destroy($id) {
        // destroy
        StoreSubscription::destroy($id);

        // back
        return back()->with('success','L’abonnement a été supprimé avec succès.');
    }   
}
