<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()->withCount('replies')->latest()->get();
        return view('client.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('client.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        auth()->user()->tickets()->create($data + ['status' => 'open']);

        return redirect()->route('client.tickets.index')
            ->with('success', 'Ticket cree avec succes.');
    }

    public function show($id)
    {
        $ticket = auth()->user()->tickets()->with('replies.user')->findOrFail($id);
        return view('client.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $ticket = auth()->user()->tickets()->findOrFail($id);
        $ticket->replies()->create([
            'message' => $data['message'],
            'user_id' => auth()->id(),
            'author_type' => 'client',
        ]);

        if ($ticket->status === 'answered') {
            $ticket->update(['status' => 'waiting']);
        }

        return back()->with('success', 'Reponse envoyee.');
    }
}
