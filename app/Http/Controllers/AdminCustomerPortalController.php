<?php

namespace App\Http\Controllers;

use App\Models\CustomerChatMessage;
use App\Models\CustomerDemo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCustomerPortalController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'CUSTOMER')
            ->withCount([
                'demos',
                'chatMessages as unread_messages_count' => function ($query) {
                    $query->where('sender_type', 'customer')
                        ->where('is_seen', false);
                },
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customer_portals.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'CUSTOMER', 404);

        $demos = $user->demos()->latest()->get();

        $messages = $user->chatMessages()
            ->oldest()
            ->get();

        $user->chatMessages()
            ->where('sender_type', 'customer')
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        return view('admin.customer_portals.show', compact('user', 'demos', 'messages'));
    }

    public function storeDemo(Request $request, User $user)
    {
        abort_if($user->role !== 'CUSTOMER', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'demo_link' => ['required', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'admin_note' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customer_demos', 'public');
        }

        $user->demos()->create([
            'name' => $data['name'],
            'demo_link' => $data['demo_link'],
            'image' => $imagePath,
            'admin_note' => $data['admin_note'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Démo ajoutée avec succès.');
    }

    public function updateDemo(Request $request, CustomerDemo $demo)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'demo_link' => ['required', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'admin_note' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $demo->image;

        if ($request->hasFile('image')) {
            if ($demo->image) {
                Storage::disk('public')->delete($demo->image);
            }

            $imagePath = $request->file('image')->store('customer_demos', 'public');
        }

        $demo->update([
            'name' => $data['name'],
            'demo_link' => $data['demo_link'],
            'image' => $imagePath,
            'admin_note' => $data['admin_note'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Démo mise à jour avec succès.');
    }

    public function sendMessage(Request $request, User $user)
    {
        abort_if($user->role !== 'CUSTOMER', 404);

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
            'user_id' => $user->id,
            'sender_type' => 'admin',
            'message' => $data['message'] ?? '',
            'link' => $data['link'] ?? null,
            'image' => $imagePath,
            'is_seen' => true,
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

    public function destroyDemo(CustomerDemo $demo)
    {
        if ($demo->image) {
            Storage::disk('public')->delete($demo->image);
        }

        $demo->delete();

        return back()->with('success', 'Démo supprimée avec succès.');
    }

}
