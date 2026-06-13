<?php

namespace App\Http\Controllers;

use App\Models\CustomerTicket;
use Illuminate\Http\Request;

class CustomerTicketController extends Controller
{
    public function index()
    {
        // fetch
        $tickets = CustomerTicket::with(['user', 'replies'])->latest()->paginate(12);

        // view
        return view('admin.customer_tickets.index', compact('tickets'));
    }

    public function show(CustomerTicket $ticket)
    {
        // load
        $ticket->load(['user', 'replies']);

        // view
        return view('admin.customer_tickets.show', compact('ticket'));
    }

    public function reply(Request $request, CustomerTicket $ticket)
    {
        // validate
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'in:open,waiting,answered,closed'],
        ]);

        // create
        $ticket->replies()->create([
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'author_type' => 'admin',
        ]);

        // update
        $ticket->update(['status' => $data['status']]);

        // back
        return back()->with('success', 'Reponse envoyee au client.');
    }
}
