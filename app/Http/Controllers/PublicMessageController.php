<?php

namespace App\Http\Controllers;

use App\Models\PublicMessage;
use Illuminate\Http\Request;

class PublicMessageController extends Controller
{
    // PUBLIC ///////////////////////////////////////////////////////////////////////////
    public function store(Request $request)
    {
        // validate
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        // create
        PublicMessage::create($data);

        // redirect
        return redirect(url()->previous() . '#contact_section')
            ->with('success', __('messages.message_sent'));
    }
    
    // ADMIN ////////////////////////////////////////////////////////////////////////
    public function waiting(){

        // fetch
        $messages = PublicMessage::where('is_seen', false)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // view
        return view('admin/public_messages/index', [
            'messages' => $messages,
            'is_seen' => false,
            'title' => 'Messages en attente',
        ]);
    }

    public function seen(){
        // fetch
        $messages = PublicMessage::where('is_seen', true)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // view
        return view('admin/public_messages/index', [
            'messages' => $messages,
            'is_seen' => true,
            'title' => 'Messages consultés',
        ]);
    }

    public function edit(PublicMessage $message)
    {
        // view
        return view('admin/public_messages/edit', compact('message'));
    }

    public function update(Request $request, PublicMessage $message)
    {
        // validate
        $request->validate([
            'is_seen' => ['nullable', 'boolean'],
            'admin_note' => ['nullable', 'string'],
        ]);

        // is seen
        $is_seen = $request->boolean('is_seen');

        // update
        $message->update([
            'is_seen' => $is_seen,
            'admin_note' => $request->input('admin_note'),
        ]);

        // redirect
        return redirect()
            ->route($is_seen ? 'admin.public_messages.seen' : 'admin.public_messages.waiting')
            ->with('success', 'Message mis à jour avec succès.');
    }

    public function markSeen(PublicMessage $message)
    {
        // update
        $message->update([
            'is_seen' => true,
        ]);

        // back
        return back()->with('success', 'Message marqué comme consulté.');
    }

    public function markWaiting(PublicMessage $message)
    {
        // update
        $message->update([
            'is_seen' => false,
        ]);

        // back
        return back()->with('success', 'Message déplacé en attente.');
    }

    public function destroy(PublicMessage $message)
    {
        // delete
        $message->delete();

        // back
        return back()->with('success', 'Message supprimé avec succès.');
    }
}


