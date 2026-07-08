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
        ->when($request->is_published !== null && $request->is_published !== '', function ($query) use ($request) {
            $query->where('is_published', (int) $request->is_published);
        })
        ->orderByDesc('priority')
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.store_subscriptions.index', compact('subscriptions'));
}
    public function create()
    {
        return view('admin/store_subscriptions/create');
    }

    public function store(Request $req)
    {
        $validated = $req->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',
            'discount'     => 'nullable|integer|min:0',
            'priority'     => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'image'        => 'nullable',
            'type'         => 'nullable|in:key,account,activation_link',
            'category'     => 'required|in:games,gift_cards,software,subscriptions,in_game_currency,other',
        ]);

        $image_url = null;

        if ($req->hasFile('image')) {
            $image_url = $req->file('image')->store('subscriptions', 'public');
        }
        
        // is published
        $validated['is_published'] = $req->boolean('is_published');

        StoreSubscription::create([
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'price'        => $validated['price'],
            'discount'     => $validated['discount'] ?? null,
            'priority'     => $validated['priority'] ?? 0,
            'is_published' => $validated['is_published'],
            'image'        => $image_url,
            'type'         => $validated['type'] ?? null,
            'category'     => $validated['category'],
        ]);

        return redirect()->route('admin.store_subs.index')
            ->with('success', 'L’abonnement a été créé avec succès.');
    }

    public function edit($id)
    {
        $subscription = StoreSubscription::findOrFail($id);

        return view('admin/store_subscriptions/edit', compact('subscription'));
    }

    public function update(Request $req, $id)
    {
        $validated = $req->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',
            'discount'     => 'nullable|integer|min:0',
            'priority'     => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'image'        => 'nullable',
            'type'         => 'nullable|in:key,account,activation_link',
            'category'     => 'required|in:games,gift_cards,software,subscriptions,in_game_currency,other',
        ]);

        $subscription = StoreSubscription::findOrFail($id);

        $image_url = $subscription->image;

        if ($req->hasFile('image')) {
            if ($subscription->image) {
                Storage::disk('public')->delete($subscription->image);
            }

            $image_url = $req->file('image')->store('subscriptions', 'public');
        }

        // is published
        $validated['is_published'] = $req->boolean('is_published');

        $subscription->update([
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'price'        => $validated['price'],
            'discount'     => $validated['discount'] ?? null,
            'priority'     => $validated['priority'] ?? 0,
            'is_published' => $validated['is_published'],
            'image'        => $image_url,
            'type'         => $validated['type'] ?? null,
            'category'     => $validated['category'],
        ]);

        return redirect()->route('admin.store_subs.index')
            ->with('success', 'L’abonnement a été mis à jour avec succès.');
    }

    public function destroy($id)
    {
        StoreSubscription::destroy($id);

        return back()->with('success', 'L’abonnement a été supprimé avec succès.');
    }
}

