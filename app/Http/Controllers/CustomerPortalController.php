<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerPortalController extends Controller
{
    public function customerDashboard()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest()->get();
        $services = $user->services()->latest()->get();
        $subscriptions = $user->subscriptions()->latest()->get();

        return view('customer.dashboard.index', [
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










    public function customerOrders()
    {
        $orders = auth()->user()->orders()->latest()->get();
        return view('customer.orders.index', compact('orders'));
    }

    public function customerOrder($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        return view('customer.orders.show', compact('order'));
    }







    public function customerServices()
    {
        $services = auth()->user()->services()->latest()->get();
        return view('customer.services.index', compact('services'));
    }



    public function customerSubscriptions()
    {
        $subscriptions = auth()->user()->subscriptions()->orderBy('renewal_date')->get();
        return view('customer.subscriptions.index', compact('subscriptions'));
    }






    public function customerTickets()
    {
        $tickets = auth()->user()->tickets()->withCount('replies')->latest()->get();
        return view('customer.tickets.index', compact('tickets'));
    }

    public function createCustomerTicket()
    {
        return view('customer.tickets.create');
    }

    public function storeCustomerTicket(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        auth()->user()->tickets()->create($data + ['status' => 'open']);

        return redirect()->route('customer.tickets.index')
            ->with('success', 'Ticket cree avec succes.');
    }

    public function customerTicket($id)
    {
        $ticket = auth()->user()->tickets()->with('replies.user')->findOrFail($id);
        return view('customer.tickets.show', compact('ticket'));
    }

    public function replyCustomerTicket(Request $request, $id)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $ticket = auth()->user()->tickets()->findOrFail($id);
        $ticket->replies()->create([
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'author_type' => 'customer',
        ]);

        if ($ticket->status === 'answered') {
            $ticket->update(['status' => 'waiting']);
        }

        return back()->with('success', 'Reponse envoyee.');
    }











    public function editCustomerProfile()
    {
        return view('customer.profile.edit');
    }

    public function updateCustomerProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil mis a jour avec succes.');
    }
}
