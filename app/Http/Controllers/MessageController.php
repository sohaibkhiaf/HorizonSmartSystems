<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
public function waiting()
    {
        $messages = Message::where('seen', false)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'seen' => false,
            'title' => 'Waiting Messages',
        ]);
    }

    public function seen()
    {
        $messages = Message::where('seen', true)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'seen' => true,
            'title' => 'Seen Messages',
        ]);
    }

    public function edit(Message $message)
    {
        return view('admin.messages.edit', compact('message'));
    }

    public function update(Request $request, Message $message)
    {
        $request->validate([
            'seen' => ['required', 'boolean'],
            'admin_note' => ['nullable', 'string'],
        ]);

        $seen = $request->boolean('seen');

        $message->update([
            'seen' => $seen,
            'admin_note' => $request->input('admin_note'),
        ]);

        return redirect()
            ->route($seen ? 'admin.messages.seen' : 'admin.messages.waiting')
            ->with('success', 'Message updated successfully.');
    }

    public function markSeen(Message $message)
    {
        $message->update([
            'seen' => true,
        ]);

        return back()->with('success', 'Message marked as seen.');
    }

    public function markWaiting(Message $message)
    {
        $message->update([
            'seen' => false,
        ]);

        return back()->with('success', 'Message moved to waiting.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return back()->with('success', 'Message deleted successfully.');
    }
}
