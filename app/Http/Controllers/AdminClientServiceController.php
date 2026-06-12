<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use App\Models\User;
use Illuminate\Http\Request;

class AdminClientServiceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['client_id', 'status']);
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        $services = CustomerService::with('user')
            ->when($request->filled('client_id'), fn ($query) => $query->where('user_id', $request->client_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.client_services.index', compact('services', 'clients', 'filters'));
    }

    public function create()
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_services.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,maintenance,expired,suspended'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'download_link' => ['nullable', 'url', 'max:255'],
        ]);

        CustomerService::create($data);

        return redirect()->route('admin.client-services.index')
            ->with('success', 'Service client ajoute avec succes.');
    }

    public function edit(CustomerService $clientService)
    {
        $clients = User::where('role', 'CLIENT')->orderBy('name')->get();

        return view('admin.client_services.edit', [
            'service' => $clientService,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, CustomerService $clientService)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,maintenance,expired,suspended'],
            'license_key' => ['nullable', 'string', 'max:255'],
            'download_link' => ['nullable', 'url', 'max:255'],
        ]);

        $clientService->update($data);

        return redirect()->route('admin.client-services.index')
            ->with('success', 'Service client mis a jour avec succes.');
    }
}
