<?php

namespace App\Http\Controllers;

use App\Models\StoreRequest;
use App\Models\StoreSubscription;
use Illuminate\Http\Request;

class StoreRequestController extends Controller
{
    public function index()
    {
        $status = request('status');
        $search = request('search');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $allowedStatuses = [
            'pending',
            'processing',
            'available',
            'paid',
            'delivered',
            'postponed',
            'cancelled',
        ];

        $requests = StoreRequest::with('storeSubscription')
            ->when(in_array($status, $allowedStatuses), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('customer_full_name', 'like', '%' . $search . '%');
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.store_requests.index', compact(
            'requests',
            'status',
            'search',
            'dateFrom',
            'dateTo'
        ));
    }

    public function show($id)
    {
        $request = StoreRequest::with('storeSubscription')->findOrFail($id);
        $subscriptions = StoreSubscription::latest()->get();

        return view('admin.store_requests.show', compact('request', 'subscriptions'));
    }

    public function updateInfo(Request $req, $id)
    {
        $data = $req->validate([
            'store_subscription_id' => ['required', 'exists:store_subscriptions,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_full_name' => ['required', 'string', 'max:255'],
            'customer_contact' => ['required', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:500'],
        ]);

        $request = StoreRequest::findOrFail($id);
        $request->update($data);

        return back()->with('success', 'Informations de la demande mises à jour avec succès !');
    }

    public function create() {
        // fetch
        $subscriptions = StoreSubscription::all();

        // view
        return view('admin/store_requests/create', compact('subscriptions'));
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
            'status' => ['required', 'in:pending,processing,available,paid,delivered,postponed,cancelled'],
            'admin_note' => ['nullable', 'string', 'max:512'],
        ]);
        // find
        $request = StoreRequest::findOrFail($id);

        // update
        $request->update([
            'status' => $req->status,
            'admin_note' => $req->admin_note,
        ]);

        // back
        return back()->with('success', 'Statut de la demande mis à jour avec succès !');
    }


    public function destroy($id) {
        $request = StoreRequest::find($id);

        if (!$request) {
            return back()->with('error', 'Demande introuvable.');
        }

        $request->delete();

        return back()->with('success', 'Demande supprimée avec succès !');
    }
    
}
