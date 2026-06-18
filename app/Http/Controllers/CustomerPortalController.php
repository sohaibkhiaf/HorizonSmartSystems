<?php

namespace App\Http\Controllers;

use App\Models\CustomerChatMessage;
use App\Models\PublicHero;
use App\Models\PublicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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

        $services = PublicService::all();
        $hero = PublicHero::findOrFail(1);

        return view('customer.portal.index', compact('user', 'demos', 'messages', 'services', 'hero'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
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
            'link' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (empty($data['message']) && empty($data['link']) && !$request->hasFile('image')) {
            return back()->withErrors([
                'message' => 'Veuillez écrire un message, ajouter un lien ou joindre une image.',
            ]);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customer_chat', 'public');
        }

        CustomerChatMessage::create([
            'user_id' => auth()->id(),
            'sender_type' => 'customer',
            'message' => $data['message'] ?? '',
            'link' => $data['link'] ?? null,
            'image' => $imagePath,
            'is_seen' => false,
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

}
