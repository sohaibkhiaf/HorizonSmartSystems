<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;


class MessageController extends Controller
{
    public function waiting(){
        $messages = Message::where('is_seen', false)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'is_seen' => false,
            'title' => 'Messages en attente',
        ]);
    }

    public function seen(){
        
        $messages = Message::where('is_seen', true)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'is_seen' => true,
            'title' => 'Messages consultés',
        ]);
    }

    public function edit(Message $message)
    {
        return view('admin.messages.edit', compact('message'));
    }

    public function update(Request $request, Message $message)
    {
        $request->validate([
            'is_seen' => ['nullable', 'boolean'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $is_seen = $request->boolean('is_seen');

        $message->update([
            'is_seen' => $is_seen,
            'admin_note' => $request->input('admin_note'),
        ]);

        return redirect()
            ->route($is_seen ? 'admin.messages.seen' : 'admin.messages.waiting')
            ->with('success', 'Message mis à jour avec succès.');
    }

    public function markSeen(Message $message)
    {
        $message->update([
            'is_seen' => true,
        ]);

        return back()->with('success', 'Message marqué comme consulté.');
    }

    public function markWaiting(Message $message)
    {
        $message->update([
            'is_seen' => false,
        ]);

        return back()->with('success', 'Message déplacé en attente.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }
}
