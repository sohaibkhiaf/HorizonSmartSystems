<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminClientTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'replies'])->latest()->paginate(12);

        return view('admin.client_tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'replies']);

        return view('admin.client_tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'in:open,waiting,answered,closed'],
        ]);

        $ticket->replies()->create([
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'author_type' => 'admin',
        ]);

        $ticket->update(['status' => $data['status']]);

        return back()->with('success', 'Reponse envoyee au client.');
    }
}
