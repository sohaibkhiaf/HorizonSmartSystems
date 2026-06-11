<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminClientOrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['client_id', 'status', 'date_from', 'date_to']);
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        $orders = Order::with('user')
            ->when($request->filled('client_id'), fn ($query) => $query->where('user_id', $request->client_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.client_orders.index', compact('orders', 'clients', 'filters'));
    }

    public function create()
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        Order::create($data);

        return redirect()->route('admin.client-orders.index')
            ->with('success', 'Commande client ajoutee avec succes.');
    }

    public function edit(Order $clientOrder)
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_orders.edit', [
            'order' => $clientOrder,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, Order $clientOrder)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $clientOrder->update($data);

        return redirect()->route('admin.client-orders.index')
            ->with('success', 'Commande client mise a jour avec succes.');
    }
}
