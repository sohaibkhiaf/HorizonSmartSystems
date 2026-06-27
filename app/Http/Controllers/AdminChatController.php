<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AdminChatController extends Controller
{
    private array $adminRoles = ['admin', 'operator'];

    private array $participantRoles = ['admin', 'operator', 'customer'];

    public function index(Request $request)
    {
        $customers = User::query()
            ->where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role']);

        $admins = User::query()
            ->whereIn('role', $this->adminRoles)
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role']);

        $participants = User::query()
            ->whereIn('role', $this->participantRoles)
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role']);

        $groups = CustomerGroup::query()
            ->withCount('customers')
            ->with(['customers:id,name,username'])
            ->latest()
            ->get();

        $messagesQuery = ChatMessage::query()
            ->with([
                'user:id,name,username,role',
                'sender:id,name,username,role',
                'receiver:id,name,username,role',
                'customerGroup:id,name',
            ])
            ->latest();

        $this->applyParticipantFilters($messagesQuery, $request);

        if ($request->filled('customer_id')) {
            $customerId = $request->integer('customer_id');

            $messagesQuery->where(function ($query) use ($customerId) {
                $query->where('user_id', $customerId)
                    ->orWhere('sender_id', $customerId)
                    ->orWhere('receiver_id', $customerId);
            });
        }

        if ($request->filled('customer_group_id')) {
            $groupId = $request->integer('customer_group_id');

            $group = CustomerGroup::with('customers:id')->find($groupId);

            $groupCustomerIds = $group
                ? $group->customers->pluck('id')->values()
                : collect();

            $messagesQuery->where(function ($query) use ($groupId, $groupCustomerIds) {
                $query->where('customer_group_id', $groupId);

                if ($groupCustomerIds->isNotEmpty()) {
                    $query->orWhereIn('user_id', $groupCustomerIds)
                        ->orWhereIn('sender_id', $groupCustomerIds)
                        ->orWhereIn('receiver_id', $groupCustomerIds);
                }
            });
        }

        if ($request->filled('admin_id')) {
            $adminId = $request->integer('admin_id');

            $messagesQuery->where(function ($query) use ($adminId) {
                $query->where('sender_id', $adminId)
                    ->orWhere('receiver_id', $adminId)
                    ->orWhere(function ($q) {
                        // receiver_id = null means: message sent to all admins/operators.
                        $q->whereNull('receiver_id')
                            ->where(function ($receiverQuery) {
                                $receiverQuery->where('sender_type', 'customer')
                                    ->orWhere('sender_type', 'admin');
                            });
                    });
            });
        }

        if ($request->filled('type')) {
            match ($request->type) {
                'customer' => $messagesQuery
                    ->whereNotNull('user_id')
                    ->whereNull('customer_group_id'),

                'internal' => $messagesQuery
                    ->whereNull('user_id')
                    ->whereNull('customer_group_id')
                    ->where('sender_type', 'admin'),

                'broadcast' => $messagesQuery
                    ->whereNotNull('customer_group_id'),

                'unread_customer' => $messagesQuery
                    ->where('sender_type', 'customer')
                    ->where('is_seen', false),

                default => null,
            };
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $messagesQuery->where(function ($query) use ($search) {
                $query->where('message', 'like', '%' . $search . '%')
                    ->orWhere('link', 'like', '%' . $search . '%')
                    ->orWhere('sender_name', 'like', '%' . $search . '%')
                    ->orWhere('receiver_name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('sender', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('receiver', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('customerGroup', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('date_from')) {
            $messagesQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $messagesQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $messages = $messagesQuery
            ->paginate(20)
            ->withQueryString();

        // Keep page 1 as the latest 20 messages, but display that page like a chat: old -> new.
        $messages->setCollection($messages->getCollection()->reverse()->values());

        $this->markSelectedCustomerMessagesAsSeen($request);

        return view('admin.chats.index', compact(
            'customers',
            'admins',
            'participants',
            'groups',
            'messages'
        ));
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'send_to' => ['required', 'in:customer,admin,group'],

            'customer_id' => ['nullable', 'required_if:send_to,customer', 'integer', 'exists:users,id'],
            'admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_group_id' => ['nullable', 'required_if:send_to,group', 'integer', 'exists:customer_groups,id'],

            'message' => ['nullable', 'string', 'max:2000'],
            'link' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'voice_message' => ['nullable', 'file', 'mimes:mp3,wav,ogg,webm,m4a,aac,mp4', 'max:10240'],
        ]);

        $messageText = trim($data['message'] ?? '');

        if (
            $messageText === '' &&
            empty($data['link']) &&
            !$request->hasFile('image') &&
            !$request->hasFile('voice_message')
        ) {
            return back()->withErrors([
                'message' => 'Veuillez écrire un message, ajouter un lien, joindre une image ou ajouter un message vocal.',
            ])->withInput();
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_files', 'public');
        }

        $voiceMessagePath = null;

        if ($request->hasFile('voice_message')) {
            $voiceMessagePath = $request->file('voice_message')->store('chat_files', 'public');
        }

        $sender = auth()->user();

        if ($data['send_to'] === 'customer') {
            $customer = User::query()
                ->where('role', 'customer')
                ->findOrFail($data['customer_id']);

            ChatMessage::create([
                'user_id' => $customer->id,
                'sender_type' => 'admin',
                'sender_id' => $sender->id,
                'receiver_id' => $customer->id,
                'customer_group_id' => null,
                'sender_name' => $sender->name,
                'receiver_name' => $customer->name,
                'message' => $messageText,
                'link' => $data['link'] ?? null,
                'image' => $imagePath,
                'voice_message' => $voiceMessagePath,
                'is_seen' => true,
            ]);

            ChatMessage::query()
                ->where('user_id', $customer->id)
                ->where('sender_type', 'customer')
                ->where('is_seen', false)
                ->update(['is_seen' => true]);

            return back()->with('success', 'Message envoyé au client avec succès.');
        }

        if ($data['send_to'] === 'admin') {
            $receiver = null;

            if (!empty($data['admin_id'])) {
                $receiver = User::query()
                    ->whereIn('role', $this->adminRoles)
                    ->findOrFail($data['admin_id']);

                if ($receiver->id === $sender->id) {
                    return back()->withErrors([
                        'admin_id' => 'Vous ne pouvez pas envoyer un message à vous-même. Laissez le destinataire vide pour envoyer à tous les admins/opérateurs.',
                    ])->withInput();
                }
            }

            ChatMessage::create([
                'user_id' => null,
                'sender_type' => 'admin',
                'sender_id' => $sender->id,
                'receiver_id' => $receiver?->id,
                'customer_group_id' => null,
                'sender_name' => $sender->name,
                'receiver_name' => $receiver?->name ?? 'Tous les admins / opérateurs',
                'message' => $messageText,
                'link' => $data['link'] ?? null,
                'image' => $imagePath,
                'voice_message' => $voiceMessagePath,
                'is_seen' => true,
            ]);

            return back()->with(
                'success',
                $receiver
                    ? 'Message interne envoyé avec succès.'
                    : 'Message envoyé à tous les admins/opérateurs avec succès.'
            );
        }

        $group = CustomerGroup::with('customers')->findOrFail($data['customer_group_id']);

        if ($group->customers->isEmpty()) {
            return back()->withErrors([
                'customer_group_id' => 'Ce groupe ne contient aucun client.',
            ])->withInput();
        }

        DB::transaction(function () use ($group, $sender, $messageText, $data, $imagePath, $voiceMessagePath) {
            foreach ($group->customers as $customer) {
                ChatMessage::create([
                    'user_id' => $customer->id,
                    'sender_type' => 'admin',
                    'sender_id' => $sender->id,
                    'receiver_id' => $customer->id,
                    'customer_group_id' => $group->id,
                    'sender_name' => $sender->name,
                    'receiver_name' => $customer->name,
                    'message' => $messageText,
                    'link' => $data['link'] ?? null,
                    'image' => $imagePath,
                    'voice_message' => $voiceMessagePath,
                    'is_seen' => true,
                ]);
            }
        });

        return back()->with('success', 'Message diffusé au groupe avec succès.');
    }

    public function storeGroup(Request $request)
    {
        $this->abortIfNotAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:customer_groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'customers' => ['required', 'array', 'min:1'],
            'customers.*' => ['integer', 'exists:users,id'],
        ]);

        $customerIds = User::query()
            ->where('role', 'customer')
            ->whereIn('id', $data['customers'])
            ->pluck('id');

        if ($customerIds->isEmpty()) {
            return back()->withErrors([
                'customers' => 'Veuillez sélectionner au moins un client valide.',
            ])->withInput();
        }

        $group = CustomerGroup::create([
            'created_by' => auth()->id(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $group->customers()->sync($customerIds);

        return back()->with('success', 'Groupe client créé avec succès.');
    }

    public function updateGroup(Request $request, CustomerGroup $customerGroup)
    {
        $this->abortIfNotAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:customer_groups,name,' . $customerGroup->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'customers' => ['required', 'array', 'min:1'],
            'customers.*' => ['integer', 'exists:users,id'],
        ]);

        $customerIds = User::query()
            ->where('role', 'customer')
            ->whereIn('id', $data['customers'])
            ->pluck('id');

        $customerGroup->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $customerGroup->customers()->sync($customerIds);

        return back()->with('success', 'Groupe client mis à jour avec succès.');
    }

    public function destroyGroup(CustomerGroup $customerGroup)
    {
        $this->abortIfNotAdmin();

        $customerGroup->customers()->detach();
        $customerGroup->delete();

        return back()->with('success', 'Groupe client supprimé avec succès.');
    }

    private function applyParticipantFilters(Builder $messagesQuery, Request $request): void
    {
        $participantOneId = $request->filled('participant_one_id')
            ? $request->integer('participant_one_id')
            : null;

        $participantTwoId = $request->filled('participant_two_id')
            ? $request->integer('participant_two_id')
            : null;

        if (!$participantOneId && !$participantTwoId) {
            return;
        }

        $ids = collect([$participantOneId, $participantTwoId])
            ->filter()
            ->unique()
            ->values();

        $selectedParticipants = User::query()
            ->whereIn('role', $this->participantRoles)
            ->whereIn('id', $ids)
            ->get(['id', 'role'])
            ->keyBy('id');

        if ($participantOneId && !$selectedParticipants->has($participantOneId)) {
            $messagesQuery->whereRaw('1 = 0');
            return;
        }

        if ($participantTwoId && !$selectedParticipants->has($participantTwoId)) {
            $messagesQuery->whereRaw('1 = 0');
            return;
        }

        if ($participantOneId && !$participantTwoId) {
            $participantOne = $selectedParticipants->get($participantOneId);

            $messagesQuery->where(function ($query) use ($participantOneId, $participantOne) {
                $query->where('sender_id', $participantOneId);

                // Legacy support for old customer messages where sender_id was not filled.
                if ($participantOne->role === 'customer') {
                    $query->orWhere(function ($q) use ($participantOneId) {
                        $q->where('sender_type', 'customer')
                            ->where('user_id', $participantOneId)
                            ->whereNull('sender_id');
                    });
                }
            });

            return;
        }

        if (!$participantOneId && $participantTwoId) {
            $participantTwo = $selectedParticipants->get($participantTwoId);
            $participantTwoIsAdmin = in_array($participantTwo->role, $this->adminRoles, true);

            $messagesQuery->where(function ($query) use ($participantTwoId, $participantTwoIsAdmin) {
                $query->where('receiver_id', $participantTwoId);

                if ($participantTwoIsAdmin) {
                    $query->orWhere(function ($q) {
                        // receiver_id = null means: message sent to all admins/operators.
                        $q->whereNull('receiver_id')
                            ->where(function ($receiverQuery) {
                                $receiverQuery->where('sender_type', 'customer')
                                    ->orWhere('sender_type', 'admin');
                            });
                    });
                }
            });

            return;
        }

        if ($participantOneId === $participantTwoId) {
            $messagesQuery->whereRaw('1 = 0');
            return;
        }

        $participantOne = $selectedParticipants->get($participantOneId);
        $participantTwo = $selectedParticipants->get($participantTwoId);

        $oneIsAdmin = in_array($participantOne->role, $this->adminRoles, true);
        $twoIsAdmin = in_array($participantTwo->role, $this->adminRoles, true);

        $oneIsCustomer = $participantOne->role === 'customer';
        $twoIsCustomer = $participantTwo->role === 'customer';

        $validAdminCustomerPair =
            ($oneIsAdmin && $twoIsCustomer) ||
            ($twoIsAdmin && $oneIsCustomer);

        $validInternalPair = $oneIsAdmin && $twoIsAdmin;

        if (!$validAdminCustomerPair && !$validInternalPair) {
            $messagesQuery->whereRaw('1 = 0');
            return;
        }

        $messagesQuery->where(function ($query) use (
            $participantOneId,
            $participantTwoId,
            $validAdminCustomerPair,
            $validInternalPair,
            $oneIsCustomer,
            $twoIsCustomer
        ) {
            $query->where(function ($q) use ($participantOneId, $participantTwoId) {
                $q->where('sender_id', $participantOneId)
                    ->where('receiver_id', $participantTwoId);
            })
            ->orWhere(function ($q) use ($participantOneId, $participantTwoId) {
                $q->where('sender_id', $participantTwoId)
                    ->where('receiver_id', $participantOneId);
            });

            if ($validAdminCustomerPair) {
                $customerId = $oneIsCustomer ? $participantOneId : $participantTwoId;

                // Customer -> all admins/operators messages.
                $query->orWhere(function ($q) use ($customerId) {
                    $q->where('sender_type', 'customer')
                        ->where('user_id', $customerId)
                        ->whereNull('receiver_id');
                });

                // Optional legacy support for old admin messages where sender_id was null.
                $query->orWhere(function ($q) use ($customerId) {
                    $q->where('sender_type', 'admin')
                        ->whereNull('sender_id')
                        ->where('receiver_id', $customerId);
                });
            }

            if ($validInternalPair) {
                // Admin/operator -> unknown receiver is considered as sent to all admins/operators.
                $query->orWhere(function ($q) use ($participantOneId, $participantTwoId) {
                    $q->where('sender_type', 'admin')
                        ->whereIn('sender_id', [$participantOneId, $participantTwoId])
                        ->whereNull('receiver_id')
                        ->whereNull('user_id')
                        ->whereNull('customer_group_id');
                });
            }
        });
    }

    private function markSelectedCustomerMessagesAsSeen(Request $request): void
    {
        $customerIds = collect();

        if ($request->filled('customer_id')) {
            $customerIds->push($request->integer('customer_id'));
        }

        foreach (['participant_one_id', 'participant_two_id'] as $field) {
            if (!$request->filled($field)) {
                continue;
            }

            $participant = User::query()
                ->where('role', 'customer')
                ->find($request->integer($field));

            if ($participant) {
                $customerIds->push($participant->id);
            }
        }

        $customerIds = $customerIds->unique()->values();

        if ($customerIds->isEmpty()) {
            return;
        }

        ChatMessage::query()
            ->whereIn('user_id', $customerIds)
            ->where('sender_type', 'customer')
            ->where('is_seen', false)
            ->update(['is_seen' => true]);
    }

    private function abortIfNotAdmin(): void
    {
        abort_if(auth()->user()->role !== 'admin', 403);
    }
}
