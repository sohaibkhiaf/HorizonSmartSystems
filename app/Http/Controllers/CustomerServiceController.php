<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function index(Request $request)
    {
        // filter
        $filters = $request->only(['customer_id', 'status']);
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // query
        $services = CustomerService::with('user')
            ->when($request->filled('customer_id'), fn ($query) => $query->where('user_id', $request->customer_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // view
        return view('admin.customer_services.index', compact('services', 'customers', 'filters'));
    }

    public function create()
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_services.create', compact('customers'));
    }

    public function store(Request $request)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,maintenance,expired,suspended'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'download_link' => ['nullable', 'url', 'max:255'],
        ]);

        // create
        CustomerService::create($data);

        // redirect
        return redirect()->route('admin.customer_services.index')
            ->with('success', 'Service client ajoute avec succes.');
    }

    public function edit(CustomerService $service)
    {
        // fetch
        $customers = User::where('role', 'CUSTOMER')->orderBy('name')->get();

        // view
        return view('admin.customer_services.edit', [
            'service' => $service,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, CustomerService $service)
    {
        // validate
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,maintenance,expired,suspended'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'download_link' => ['nullable', 'url', 'max:255'],
        ]);

        // update
        $service->update($data);

        // redirect
        return redirect()->route('admin.customer_services.index')
            ->with('success', 'Service client mis a jour avec succes.');
    }
}
