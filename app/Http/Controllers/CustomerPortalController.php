<?php

namespace App\Http\Controllers;

use App\Models\CustomerChatMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerPortalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $demos = $user->demos()
            ->where('is_active', true)
            ->latest()
            ->get();

        $messages = $user->chatMessages()
            ->oldest()
            ->get();

        return view('customer.portal.index', compact('user', 'demos', 'messages'));
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        auth()->user()->update($data);

        return back()->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        CustomerChatMessage::create([
            'user_id' => auth()->id(),
            'sender_type' => 'customer',
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

}
