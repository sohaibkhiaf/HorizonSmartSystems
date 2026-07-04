<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\CustomerDemo;
use App\Models\PublicHero;
use App\Models\PublicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerPortalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $demos = CustomerDemo::with('publicSoftware')
            ->where('is_active', true)
            ->latest()
            ->get();

        $messages = $user->chatMessages()
            ->with(['sender', 'receiver', 'customerGroup'])
            ->latest()
            ->paginate(99);

        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        return view('customer.portal.index', compact('user', 'demos', 'messages', 'services', 'hero'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'size:10'],
            'company' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', 'min:4'],
        ]);

        $user->phone = $data['phone'] ?? null;
        $user->company = $data['company'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'link' => ['nullable', 'url', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'voice_message' => ['nullable', 'file', 'mimes:mp3,wav,ogg,webm,m4a,aac,mp4', 'max:10240'],
        ]);

        if (
            empty($data['message']) &&
            empty($data['link']) &&
            !$request->hasFile('image') &&
            !$request->hasFile('voice_message')
        ) {
            return back()->withErrors([
                'message' => 'Veuillez écrire un message, ajouter un lien, joindre une image ou ajouter un message vocal.',
            ]);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_files', 'public');
        }

        $voiceMessagePath = null;

        if ($request->hasFile('voice_message')) {
            $voiceMessagePath = $request->file('voice_message')->store('chat_files', 'public');
        }

        ChatMessage::create([
            'user_id' => auth()->id(),

            'sender_type' => 'customer',
            'sender_id' => auth()->id(),
            'receiver_id' => null,

            'sender_name' => auth()->user()->name,
            'receiver_name' => 'Administration',

            'message' => $data['message'] ?? '',
            'link' => $data['link'] ?? null,
            'image' => $imagePath,
            'voice_message' => $voiceMessagePath,

            'is_seen' => false,
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

}
