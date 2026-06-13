<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        // filter
        $filters = $request->only(['customer_id', 'status', 'date_from', 'date_to']);
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // fetch
        $orders = CustomerOrder::with('user')
            ->when($request->filled('customer_id'), fn ($query) => $query->where('user_id', $request->customer_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // view
        return view('admin.customer_orders.index', compact('orders', 'customers', 'filters'));
    }

    public function create()
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        // create
        CustomerOrder::create($data);

        // redirect
        return redirect()->route('admin.customer_orders.index')
            ->with('success', 'Commande client ajoutee avec succes.');
    }

    public function edit(CustomerOrder $order)
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_orders.edit', [
            'order' => $order,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, CustomerOrder $order)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        // update
        $order->update($data);

        // redirect
        return redirect()->route('admin.customer_orders.index')
            ->with('success', 'Commande client mise a jour avec succes.');
    }
}
