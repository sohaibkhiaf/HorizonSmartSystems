<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerChatMessage;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    private array $adminRoles = ['admin', 'operator'];

    public function index(Request $request)
    {
        $customers = User::query()
            ->where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $admins = User::query()
            ->whereIn('role', $this->adminRoles)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $participants = User::query()
            ->whereIn('role', array_merge($this->adminRoles, ['customer']))
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $groups = CustomerGroup::query()
            ->withCount('customers')
            ->with(['customers:id,name,email'])
            ->latest()
            ->get();

        $messagesQuery = CustomerChatMessage::query()
            ->with([
                'user:id,name,email,role',
                'sender:id,name,email,role',
                'receiver:id,name,email,role',
                'customerGroup:id,name',
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Filter messages between two users/entities
        |--------------------------------------------------------------------------
        | Supported:
        | - admin/operator <-> customer
        | - admin/operator <-> admin/operator
        |
        | Important:
        | Customer messages have receiver_id = null because they are sent to all admins.
        | So when filtering admin/operator + customer, we also include:
        | customer sender_id + receiver_id null + user_id customer_id.
        */
        if ($request->filled('participant_one_id') && $request->filled('participant_two_id')) {
            $participantOneId = (int) $request->participant_one_id;
            $participantTwoId = (int) $request->participant_two_id;

            if ($participantOneId === $participantTwoId) {
                $messagesQuery->whereRaw('1 = 0');
            } else {
                $participantOne = User::query()
                    ->whereIn('role', array_merge($this->adminRoles, ['customer']))
                    ->find($participantOneId);

                $participantTwo = User::query()
                    ->whereIn('role', array_merge($this->adminRoles, ['customer']))
                    ->find($participantTwoId);

                if (!$participantOne || !$participantTwo) {
                    $messagesQuery->whereRaw('1 = 0');
                } else {
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
                    } else {
                        $customer = null;

                        if ($validAdminCustomerPair) {
                            $customer = $oneIsCustomer ? $participantOne : $participantTwo;
                        }

                        $messagesQuery->where(function ($query) use (
                            $participantOneId,
                            $participantTwoId,
                            $validAdminCustomerPair,
                            $customer
                        ) {
                            $query->where(function ($q) use ($participantOneId, $participantTwoId) {
                                $q->where('sender_id', $participantOneId)
                                    ->where('receiver_id', $participantTwoId);
                            })
                            ->orWhere(function ($q) use ($participantOneId, $participantTwoId) {
                                $q->where('sender_id', $participantTwoId)
                                    ->where('receiver_id', $participantOneId);
                            });

                            // Customer -> all admins messages.
                            if ($validAdminCustomerPair && $customer) {
                                $query->orWhere(function ($q) use ($customer) {
                                    $q->where('sender_type', 'customer')
                                        ->where('sender_id', $customer->id)
                                        ->where('user_id', $customer->id)
                                        ->whereNull('receiver_id');
                                });

                                // Optional legacy support for old admin messages where sender_id was null.
                                $query->orWhere(function ($q) use ($customer) {
                                    $q->where('sender_type', 'admin')
                                        ->whereNull('sender_id')
                                        ->where('receiver_id', $customer->id);
                                });
                            }
                        });
                    }
                }
            }
        }

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
                    ->orWhere('receiver_id', $adminId);
            });
        }

        if ($request->filled('type')) {
            match ($request->type) {
                'customer' => $messagesQuery
                    ->whereNotNull('user_id')
                    ->whereNull('customer_group_id'),

                'internal' => $messagesQuery
                    ->whereNull('user_id')
                    ->whereNotNull('receiver_id'),

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
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('sender', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('receiver', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
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

        if ($request->filled('customer_id')) {
            CustomerChatMessage::query()
                ->where('user_id', $request->integer('customer_id'))
                ->where('sender_type', 'customer')
                ->where('is_seen', false)
                ->update(['is_seen' => true]);
        }

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
            'admin_id' => ['nullable', 'required_if:send_to,admin', 'integer', 'exists:users,id'],
            'customer_group_id' => ['nullable', 'required_if:send_to,group', 'integer', 'exists:customer_groups,id'],

            'message' => ['nullable', 'string', 'max:2000'],
            'link' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $messageText = trim($data['message'] ?? '');

        if ($messageText === '' && empty($data['link']) && !$request->hasFile('image')) {
            return back()->withErrors([
                'message' => 'Veuillez écrire un message, ajouter un lien ou joindre une image.',
            ])->withInput();
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('customer_chat', 'public');
        }

        $sender = auth()->user();

        if ($data['send_to'] === 'customer') {
            $customer = User::query()
                ->where('role', 'customer')
                ->findOrFail($data['customer_id']);

            CustomerChatMessage::create([
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
                'is_seen' => true,
            ]);

            CustomerChatMessage::query()
                ->where('user_id', $customer->id)
                ->where('sender_type', 'customer')
                ->where('is_seen', false)
                ->update(['is_seen' => true]);

            return back()->with('success', 'Message envoyé au client avec succès.');
        }

        if ($data['send_to'] === 'admin') {
            $receiver = User::query()
                ->whereIn('role', $this->adminRoles)
                ->findOrFail($data['admin_id']);

            if ($receiver->id === $sender->id) {
                return back()->withErrors([
                    'admin_id' => 'Vous ne pouvez pas envoyer un message à vous-même.',
                ])->withInput();
            }

            CustomerChatMessage::create([
                'user_id' => null,
                'sender_type' => 'admin',
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'customer_group_id' => null,
                'sender_name' => $sender->name,
                'receiver_name' => $receiver->name,
                'message' => $messageText,
                'link' => $data['link'] ?? null,
                'image' => $imagePath,
                'is_seen' => true,
            ]);

            return back()->with('success', 'Message interne envoyé avec succès.');
        }

        $group = CustomerGroup::with('customers')->findOrFail($data['customer_group_id']);

        if ($group->customers->isEmpty()) {
            return back()->withErrors([
                'customer_group_id' => 'Ce groupe ne contient aucun client.',
            ])->withInput();
        }

        DB::transaction(function () use ($group, $sender, $messageText, $data, $imagePath) {
            foreach ($group->customers as $customer) {
                CustomerChatMessage::create([
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

    private function abortIfNotAdmin(): void
    {
        abort_if(auth()->user()->role !== 'admin', 403);
    }
}

