@extends('admin.layouts.admin')

@section('page_title', 'Messagerie')
@section('page_subtitle', 'Messages clients, messages internes et diffusion par groupes')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/chats/index.css') }}">
@endsection

@section('content')

@if(session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="flash-error">
        {{ $errors->first() }}
    </div>
@endif

<div class="chat-page">

    <div class="container filter-container">
        <div class="header">
            <h4>Filtres</h4>
        </div>

        <div class="body">
            <form method="GET" action="{{ route('admin.chats.index') }}" class="filter-form">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Recherche message, nom, email...">

                <select name="customer_id">
                    <option value="">Tous les clients</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
                            {{ $customer->name }} — {{ $customer->email }}
                        </option>
                    @endforeach
                </select>

                <select name="customer_group_id">
                    <option value="">Tous les groupes</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected(request('customer_group_id') == $group->id)>
                            {{ $group->name }} ({{ $group->customers_count }})
                        </option>
                    @endforeach
                </select>

                <select name="admin_id">
                    <option value="">Tous les admins / opérateurs</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" @selected(request('admin_id') == $admin->id)>
                            {{ $admin->name }} — {{ ucfirst($admin->role) }}
                        </option>
                    @endforeach
                </select>

                <select name="type">
                    <option value="">Tous les types</option>
                    <option value="customer" @selected(request('type') === 'customer')>Messages clients directs</option>
                    <option value="internal" @selected(request('type') === 'internal')>Messages internes</option>
                    <option value="broadcast" @selected(request('type') === 'broadcast')>Diffusions groupes</option>
                    <option value="unread_customer" @selected(request('type') === 'unread_customer')>Clients non lus</option>
                </select>

                <select name="participant_one_id">
                    <option value="">Participant 1</option>

                    <optgroup label="Admins / Opérateurs">
                        @foreach($participants->whereIn('role', ['admin', 'operator']) as $participant)
                            <option value="{{ $participant->id }}"
                                @selected(request('participant_one_id') == $participant->id)>
                                {{ $participant->name }} — {{ ucfirst($participant->role) }}
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Clients">
                        @foreach($participants->where('role', 'customer') as $participant)
                            <option value="{{ $participant->id }}"
                                @selected(request('participant_one_id') == $participant->id)>
                                {{ $participant->name }} — {{ $participant->email }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>

                <select name="participant_two_id">
                    <option value="">Participant 2</option>

                    <optgroup label="Admins / Opérateurs">
                        @foreach($participants->whereIn('role', ['admin', 'operator']) as $participant)
                            <option value="{{ $participant->id }}"
                                @selected(request('participant_two_id') == $participant->id)>
                                {{ $participant->name }} — {{ ucfirst($participant->role) }}
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Clients">
                        @foreach($participants->where('role', 'customer') as $participant)
                            <option value="{{ $participant->id }}"
                                @selected(request('participant_two_id') == $participant->id)>
                                {{ $participant->name }} — {{ $participant->email }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}">
                <input type="date" name="date_to" value="{{ request('date_to') }}">

                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter"></i>
                    Filtrer
                </button>

                <a href="{{ route('admin.chats.index') }}" class="btn-secondary">
                    Réinitialiser
                </a>
            </form>
        </div>
    </div>

    <div class="chat-grid">

        <div class="left-column">

            <div class="container">
                <div class="header">
                    <h4>Envoyer un message</h4>
                </div>

                <div class="body">
                    <form method="POST"
                          action="{{ route('admin.chats.messages.store') }}"
                          class="compose-form"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Envoyer à <span class="required">*</span></label>
                            <select name="send_to" id="sendTo" required>
                                <option value="customer" @selected(old('send_to', 'customer') === 'customer')>
                                    Client
                                </option>
                                <option value="admin" @selected(old('send_to') === 'admin')>
                                    Admin / Opérateur
                                </option>
                                <option value="group" @selected(old('send_to') === 'group')>
                                    Groupe de clients
                                </option>
                            </select>
                        </div>

                        <div class="form-group recipient-field" data-recipient="customer">
                            <label>Client</label>
                            <select name="customer_id">
                                <option value="">Sélectionner un client</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        @selected(old('customer_id', request('customer_id')) == $customer->id)>
                                        {{ $customer->name }} — {{ $customer->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group recipient-field" data-recipient="admin">
                            <label>Admin / Opérateur</label>
                            <select name="admin_id">
                                <option value="">Sélectionner une personne</option>
                                @foreach($admins as $admin)
                                    @if($admin->id !== auth()->id())
                                        <option value="{{ $admin->id }}" @selected(old('admin_id') == $admin->id)>
                                            {{ $admin->name }} — {{ ucfirst($admin->role) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group recipient-field" data-recipient="group">
                            <label>Groupe</label>
                            <select name="customer_group_id">
                                <option value="">Sélectionner un groupe</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" @selected(old('customer_group_id') == $group->id)>
                                        {{ $group->name }} — {{ $group->customers_count }} client(s)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" placeholder="Écrire un message...">{{ old('message') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Lien optionnel</label>
                            <input type="url" name="link" value="{{ old('link') }}" placeholder="https://example.com">
                        </div>

                        <div class="form-group">
                            <label>Image optionnelle</label>
                            <input type="file" name="image" accept="image/*">
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-paper-plane"></i>
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>

            <div class="container groups-container">
                <div class="header">
                    <h4>Groupes de clients</h4>
                </div>

                <div class="body">

                    @if(auth()->user()->role === 'admin')
                        <form method="POST"
                              action="{{ route('admin.chats.groups.store') }}"
                              class="group-form">
                            @csrf

                            <div class="form-group">
                                <label>Nom du groupe <span class="required">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Clients Wili" required>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" placeholder="Description optionnelle...">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Clients <span class="required">*</span></label>
                                <select name="customers[]" multiple required class="multi-select">
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->name }} — {{ $customer->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <small>Maintenez CTRL pour sélectionner plusieurs clients.</small>
                            </div>

                            <button type="submit" class="btn-primary">
                                Créer le groupe
                            </button>
                        </form>
                    @else
                        <p class="empty">
                            La création et la modification des groupes sont réservées aux administrateurs.
                        </p>
                    @endif

                    <div class="group-list">
                        @forelse($groups as $group)
                            <div class="group-card">
                                <div class="group-top">
                                    <div>
                                        <h5>{{ $group->name }}</h5>
                                        <p>{{ $group->customers_count }} client(s)</p>
                                    </div>

                                    <a href="{{ route('admin.chats.index', ['customer_group_id' => $group->id]) }}"
                                       class="mini-link">
                                        Voir messages
                                    </a>
                                </div>

                                @if($group->description)
                                    <p class="group-description">{{ $group->description }}</p>
                                @endif

                                @if($group->customers->isNotEmpty())
                                    <div class="group-customers">
                                        @foreach($group->customers->take(6) as $customer)
                                            <span>{{ $customer->name }}</span>
                                        @endforeach

                                        @if($group->customers->count() > 6)
                                            <span>+{{ $group->customers->count() - 6 }}</span>
                                        @endif
                                    </div>
                                @endif

                                @if(auth()->user()->role === 'admin')
                                    <details class="group-edit">
                                        <summary>Modifier</summary>

                                        <form method="POST"
                                              action="{{ route('admin.chats.groups.update', $group->id) }}"
                                              class="group-form">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <label>Nom</label>
                                                <input type="text" name="name" value="{{ $group->name }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description">{{ $group->description }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>Clients</label>
                                                @php
                                                    $selectedCustomers = $group->customers->pluck('id')->toArray();
                                                @endphp

                                                <select name="customers[]" multiple required class="multi-select">
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            @selected(in_array($customer->id, $selectedCustomers))>
                                                            {{ $customer->name }} — {{ $customer->email }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <button type="submit" class="btn-primary">
                                                Enregistrer
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.chats.groups.destroy', $group->id) }}"
                                              class="delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn-danger"
                                                    onclick="return confirm('Supprimer ce groupe ?')">
                                                Supprimer le groupe
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            </div>
                        @empty
                            <p class="empty">Aucun groupe créé.</p>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

        <div class="right-column">
            <div class="container messages-container">
                <div class="header">
                    <h4>Messages</h4>
                </div>

                <div class="body">

                    <div class="refresh-notice" id="adminRefreshNotice">
                        <div>
                            <i class="fa-solid fa-arrow-rotate-right"></i>
                            Actualisez la page pour voir les derniers messages.
                        </div>

                        <a href="{{ route('admin.chats.index', request()->except('page')) }}">
                            Rafraîchir
                        </a>
                    </div>

                    <div class="messages-list">
                        @forelse($messages as $message)

                            @php
                                $typeClass = $message->customer_group_id
                                    ? 'broadcast'
                                    : ($message->user_id ? 'customer-message' : 'internal-message');
                            @endphp

                            <div class="message-card {{ $typeClass }}">
                                <div class="message-top">
                                    <div class="message-route">
                                        <strong>{{ $message->sender_display_name }}</strong>
                                        <i class="fas fa-arrow-right"></i>
                                        <strong>{{ $message->receiver_display_name }}</strong>
                                    </div>

                                    <span class="message-date">
                                        {{ $message->sender_display_name }}
                                        →
                                        {{ $message->receiver_display_name }}
                                        ·
                                        {{ $message->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div class="message-badges">
                                    @if($message->customer_group_id)
                                        <span class="badge broadcast-badge">Diffusion groupe</span>
                                    @elseif($message->user_id)
                                        <span class="badge customer-badge">Client</span>
                                    @else
                                        <span class="badge internal-badge">Interne</span>
                                    @endif

                                    @if($message->sender_type === 'customer' && !$message->is_seen)
                                        <span class="badge unread-badge">Non lu</span>
                                    @endif

                                    @if($message->user)
                                        <span class="badge neutral-badge">
                                            Client: {{ $message->user->name }}
                                        </span>
                                    @endif
                                </div>

                                @if($message->message)
                                    <p class="message-text">{{ $message->message }}</p>
                                @endif

                                @if($message->link)
                                    <a href="{{ $message->link }}" target="_blank" class="message-link">
                                        {{ $message->link }}
                                    </a>
                                @endif

                                @if($message->image)
                                    <a href="{{ asset('uploads/' . $message->image) }}" target="_blank">
                                        <img src="{{ asset('uploads/' . $message->image) }}"
                                             alt="Image message"
                                             class="message-image">
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="empty">Aucun message trouvé.</p>
                        @endforelse
                    </div>

                    <div class="pagination-wrapper">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sendTo = document.getElementById('sendTo');
    const fields = document.querySelectorAll('.recipient-field');

    function updateRecipientFields() {
        const value = sendTo.value;

        fields.forEach(field => {
            field.style.display = field.dataset.recipient === value ? 'block' : 'none';
        });
    }

    if (sendTo) {
        updateRecipientFields();
        sendTo.addEventListener('change', updateRecipientFields);
    }
});



document.addEventListener('DOMContentLoaded', function () {
    const refreshNotice = document.getElementById('adminRefreshNotice');

    setTimeout(function () {
        if (refreshNotice) {
            refreshNotice.classList.add('is-visible');
        }
    }, 12000);
});
</script>

@endsection

