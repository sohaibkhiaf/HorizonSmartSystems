<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerPortalController extends Controller
{
    // DASHBOARD //////////////////////////////////////////////////////////////////////
    public function customerDashboard()
    {
        // fetch
        $user = auth()->user();
        $orders = $user->orders()->latest()->get();
        $services = $user->services()->latest()->get();
        $subscriptions = $user->subscriptions()->latest()->get();

        // view
        return view('customer/dashboard', [
            'orders_count' => $orders->count(),
            'orders_total' => $orders->sum('price'),
            'services_count' => $services->where('status', 'active')->count(),
            'subscriptions_count' => $subscriptions->where('status', 'active')->count(),
            'open_tickets_count' => $user->tickets()->whereIn('status', ['open', 'waiting'])->count(),
            'recent_orders' => $orders->take(5),
            'recent_services' => $services->take(4),
            'expiring_subscriptions' => $user->subscriptions()
                ->whereDate('renewal_date', '<=', now()->addDays(7))
                ->orderBy('renewal_date')
                ->get()
        ]);
    }


    // ORDERS //////////////////////////////////////////////////////////////////////

    public function customerOrders()
    {
        // fetch
        $orders = auth()->user()->orders()->latest()->get();

        // view
        return view('customer/orders/index', compact('orders'));
    }

    public function customerOrder($id)
    {
        // fetch
        $order = auth()->user()->orders()->findOrFail($id);

        // view
        return view('customer/orders/show', compact('order'));
    }


    // SERVICES //////////////////////////////////////////////////////////////////////

    public function customerServices()
    {
        // fetch
        $services = auth()->user()->services()->latest()->get();

        // view
        return view('customer/services/index', compact('services'));
    }


    // SUBSCRIPTIONS //////////////////////////////////////////////////////////////////////
    public function customerSubscriptions()
    {
        // fetch
        $subscriptions = auth()->user()->subscriptions()->orderBy('renewal_date')->get();

        // view
        return view('customer/subscriptions/index', compact('subscriptions'));
    }


    // TICKETS //////////////////////////////////////////////////////////////////////

    public function customerTickets()
    {
        // fetch
        $tickets = auth()->user()->tickets()->withCount('replies')->latest()->get();

        // view
        return view('customer/tickets/index', compact('tickets'));
    }

    public function createCustomerTicket()
    {
        // view
        return view('customer/tickets/create');
    }

    public function storeCustomerTicket(Request $request)
    {
        // validate
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // create
        auth()->user()->tickets()->create($data + ['status' => 'open']);

        // redirect
        return redirect()->route('customer.tickets.index')
            ->with('success', 'Ticket cree avec succes.');
    }

    public function customerTicket($id)
    {
        // find
        $ticket = auth()->user()->tickets()->with('replies.user')->findOrFail($id);

        // view
        return view('customer/tickets/show', compact('ticket'));
    }

    public function replyCustomerTicket(Request $request, $id)
    {
        // validate
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // find
        $ticket = auth()->user()->tickets()->findOrFail($id);
        // create
        $ticket->replies()->create([
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'author_type' => 'customer',
        ]);

        // update
        if ($ticket->status === 'answered') {
            $ticket->update(['status' => 'waiting']);
        }

        // back
        return back()->with('success', 'Reponse envoyee.');
    }


    // PROFILE //////////////////////////////////////////////////////////////////////

    public function editCustomerProfile()
    {
        // view
        return view('customer/profile/edit');
    }

    public function updateCustomerProfile(Request $request)
    {
        // user
        $user = auth()->user();

        // validate
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        // update
        $user->update($data);

        // back
        return back()->with('success', 'Profil mis a jour avec succes.');
    }
}
