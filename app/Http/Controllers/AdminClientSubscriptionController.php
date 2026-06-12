<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminClientSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['client_id', 'status']);
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        $subscriptions = CustomerSubscription::with('user')
            ->when($request->filled('client_id'), fn ($query) => $query->where('user_id', $request->client_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.client_subscriptions.index', compact('subscriptions', 'clients', 'filters'));
    }

    public function create()
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_subscriptions.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'renewal_date' => ['required', 'date'],
            'status' => ['required', 'in:active,pending,cancelled,expired'],
        ]);

        CustomerSubscription::create($data);

        return redirect()->route('admin.client-subscriptions.index')
            ->with('success', 'Abonnement client ajoute avec succes.');
    }

    public function edit(CustomerSubscription $clientSubscription)
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_subscriptions.edit', [
            'subscription' => $clientSubscription,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, CustomerSubscription $clientSubscription)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'renewal_date' => ['required', 'date'],
            'status' => ['required', 'in:active,pending,cancelled,expired'],
        ]);

        $clientSubscription->update($data);

        return redirect()->route('admin.client-subscriptions.index')
            ->with('success', 'Abonnement client mis a jour avec succes.');
    }
}
