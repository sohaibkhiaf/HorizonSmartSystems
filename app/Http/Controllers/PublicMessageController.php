<?php

namespace App\Http\Controllers;

use App\Models\PublicMessage;
use Illuminate\Http\Request;

class PublicMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        PublicMessage::create($data);

        return redirect(url()->previous() . '#contactSection')
            ->with('success', __('messages.message_sent'));
    }
    
    public function waiting(){
        $messages = PublicMessage::where('is_seen', false)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'is_seen' => false,
            'title' => 'Messages en attente',
        ]);
    }

    public function seen(){
        
        $messages = PublicMessage::where('is_seen', true)
            ->latest()
            ->paginate(9);

        return view('admin.messages.index', [
            'messages' => $messages,
            'is_seen' => true,
            'title' => 'Messages consultés',
        ]);
    }

    public function edit(PublicMessage $message)
    {
        return view('admin.messages.edit', compact('message'));
    }

    public function update(Request $request, PublicMessage $message)
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

    public function markSeen(PublicMessage $message)
    {
        $message->update([
            'is_seen' => true,
        ]);

        return back()->with('success', 'Message marqué comme consulté.');
    }

    public function markWaiting(PublicMessage $message)
    {
        $message->update([
            'is_seen' => false,
        ]);

        return back()->with('success', 'Message déplacé en attente.');
    }

    public function destroy(PublicMessage $message)
    {
        $message->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }
}


