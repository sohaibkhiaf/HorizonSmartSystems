<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        // filter
        $filters = $request->only(['customer_id', 'status']);
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // fetch
        $subscriptions = CustomerSubscription::with('user')
            ->when($request->filled('customer_id'), fn ($query) => $query->where('user_id', $request->customer_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // view
        return view('admin.customer_subscriptions.index', compact('subscriptions', 'customers', 'filters'));
    }

    public function create()
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_subscriptions.create', compact('customers'));
    }

    public function store(Request $request)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'renewal_date' => ['required', 'date'],
            'status' => ['required', 'in:active,pending,cancelled,expired'],
        ]);

        // create
        CustomerSubscription::create($data);

        // redirect
        return redirect()->route('admin.customer_subscriptions.index')
            ->with('success', 'Abonnement client ajoute avec succes.');
    }

    public function edit(CustomerSubscription $subscription)
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_subscriptions.edit', [
            'subscription' => $subscription,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, CustomerSubscription $subscription)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'renewal_date' => ['required', 'date'],
            'status' => ['required', 'in:active,pending,cancelled,expired'],
        ]);

        // update
        $subscription->update($data);

        // redirect
        return redirect()->route('admin.customer_subscriptions.index')
            ->with('success', 'Abonnement client mis a jour avec succes.');
    }
}
