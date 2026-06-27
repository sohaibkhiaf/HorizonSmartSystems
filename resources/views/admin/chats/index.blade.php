@extends('admin.layouts.admin')

@section('page_title', 'Messagerie')
@section('page_subtitle', 'Messages clients, messages internes et diffusion par groupes')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/chats/index.css') }}">
@endsection

@section('content')

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
                    placeholder="Recherche message, nom, username...">

                <select name="customer_id">
                    <option value="">Tous les clients</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
                            {{ $customer->name }} — {{ $customer->username }}
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
                    <option value="">Participant 1 (expéditeur)</option>

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
                                {{ $participant->name }} — {{ $participant->username }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>

                <select name="participant_two_id">
                    <option value="">Participant 2 (destinataire)</option>

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
                                {{ $participant->name }} — {{ $participant->username }}
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
                                        {{ $customer->name }} — {{ $customer->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group recipient-field" data-recipient="admin">
                            <label>Admin / Opérateur</label>
                            <select name="admin_id">
                                <option value="">Tous les admins / opérateurs</option>
                                @foreach($admins as $admin)
                                    @if($admin->id !== auth()->id())
                                        <option value="{{ $admin->id }}" @selected(old('admin_id') == $admin->id)>
                                            {{ $admin->name }} — {{ ucfirst($admin->role) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small>Laissez vide pour envoyer un message interne avec destinataire inconnu.</small>
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

                        <div class="form-group voice-message-group">
                            <label>Message vocal optionnel</label>

                            <input
                                type="file"
                                name="voice_message"
                                id="adminVoiceMessage"
                                accept="audio/*,.webm,.ogg,.mp3,.wav,.m4a,.aac,.mp4">

                            <div class="voice-recorder" data-target="adminVoiceMessage">
                                <button type="button" class="voice-record-btn" data-action="record">
                                    <i class="fa-solid fa-microphone"></i>
                                    Enregistrer
                                </button>

                                <button type="button" class="voice-stop-btn" data-action="stop" disabled>
                                    <i class="fa-solid fa-stop"></i>
                                    Arrêter
                                </button>

                                <button type="button" class="voice-clear-btn" data-action="clear" disabled>
                                    <i class="fa-solid fa-trash"></i>
                                    Supprimer
                                </button>

                                <span class="voice-status">Aucun message vocal</span>

                                <audio controls class="voice-preview"></audio>
                            </div>

                            <small>Vous pouvez importer un fichier audio ou enregistrer directement depuis le navigateur.</small>
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
                                            {{ $customer->name }} — {{ $customer->username }}
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
                                                            {{ $customer->name }} — {{ $customer->username }}
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

                    <div class="messages-list" id="adminMessagesList">
                        @forelse($messages as $message)
                            @php
                                $bubbleClass = $message->sender_type === 'customer' ? 'customer' : 'admin';

                                $messageTypeClass = 'message-customer-admin';

                                if ($message->customer_group_id) {
                                    $messageTypeClass = 'message-broadcast';
                                } elseif ($message->sender_type === 'admin' && !$message->user_id) {
                                    $messageTypeClass = 'message-internal';
                                } elseif ($message->sender_type === 'admin') {
                                    $messageTypeClass = 'message-admin-customer';
                                }


                                $typeLabel = 'Client vers administration';

                                if ($message->customer_group_id) {
                                    $typeLabel = 'Diffusion groupe';
                                } elseif ($message->sender_type === 'admin' && !$message->user_id && !$message->customer_group_id) {
                                    $typeLabel = 'Interne';
                                } elseif ($message->sender_type === 'admin') {
                                    $typeLabel = 'Administration vers client';
                                }

                                $receiverName = $message->receiver_display_name;

                                if (!$message->receiver_id && $message->sender_type === 'admin' && !$message->user_id && !$message->customer_group_id) {
                                    $receiverName = 'Tous les admins / opérateurs';
                                }

                                if (!$message->receiver_id && $message->sender_type === 'customer') {
                                    $receiverName = 'Tous les admins / opérateurs';
                                }

                                $messageImageUrl = null;

                                if ($message->image) {
                                    $messageImageUrl =asset('uploads/' . $message->image);
                                }

                                $messageVoiceUrl = null;

                                if ($message->voice_message) {
                                    $messageVoiceUrl = asset('uploads/' . $message->voice_message);
                                }
                            @endphp

                            <div class="message-card {{ $bubbleClass }} {{ $messageTypeClass }}">
                                <div class="message-content">

                                    @if($message->message)
                                        <p class="message-text">{{ $message->message }}</p>
                                    @endif

                                    @if($message->link)
                                        <a href="{{ $message->link }}" target="_blank" class="message-link">
                                            {{ $message->link }}
                                        </a>
                                    @endif

                                    @if($messageImageUrl)
                                        <a href="{{ $messageImageUrl }}" target="_blank">
                                            <img src="{{ $messageImageUrl }}"
                                                 alt="Image message"
                                                 class="message-image">
                                        </a>
                                    @endif

                                    @if($messageVoiceUrl)
                                        <audio controls preload="metadata" class="message-audio" src="{{ $messageVoiceUrl }}">
                                            Votre navigateur ne supporte pas la lecture audio.
                                        </audio>
                                    @endif

                                    <span class="message-meta">
                                        
                                    </span>
                                    <span class="message-meta">
                                        {{ $message->sender_display_name }}
                                        {{ ' → ' . $receiverName }}
                                        {{ ' · ' . $message->created_at->format('d/m/Y H:i') }}
                                        <br>
                                        {{ $typeLabel }} 
                                        {{ $message->customerGroup != null ? ' · '. $message->customerGroup->name : '' }} 
                                        {{ $message->sender_type === 'customer' && !$message->is_seen ? ' · '.'Non répondu' : '' }} 
                                        {{ $message->user ? ' · Client concerné : '.$message->user->name : '' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="empty">Aucun message trouvé.</p>
                        @endforelse
                    </div>

                    <div class="refresh-notice" id="adminRefreshNotice">
                        <div>
                            <i class="fa-solid fa-arrow-rotate-right"></i>
                            Actualisez la page pour voir les derniers messages.
                        </div>

                        <a href="{{ route('admin.chats.index', request()->except('page')) }}">
                            Rafraîchir
                        </a>
                    </div>

                    @if ($messages->hasPages())
                        <div class="navigator-container">
                            @if ($messages->currentPage() > 1)
                                <a href="{{ $messages->previousPageUrl() }}">
                                    <i class="fa-solid fa-angle-left"></i>
                                </a>
                            @else
                                <a href="{{ $messages->url($messages->currentPage()) }}" class="disabled">
                                    <i class="fa-solid fa-angle-left"></i>
                                </a>
                            @endif

                            @for ($i = 1; $i <= $messages->lastPage(); $i++)
                                <a href="{{ $messages->url($i) }}"
                                   class="{{ $messages->currentPage() == $i ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if ($messages->currentPage() < $messages->lastPage())
                                <a href="{{ $messages->nextPageUrl() }}">
                                    <i class="fa-solid fa-angle-right"></i>
                                </a>
                            @else
                                <a href="{{ $messages->url($messages->currentPage()) }}" class="disabled">
                                    <i class="fa-solid fa-angle-right"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sendTo = document.getElementById('sendTo');
    const fields = document.querySelectorAll('.recipient-field');
    const refreshNotice = document.getElementById('adminRefreshNotice');
    const messagesList = document.getElementById('adminMessagesList');

    function updateRecipientFields() {
        if (!sendTo) return;

        const value = sendTo.value;

        fields.forEach(field => {
            field.style.display = field.dataset.recipient === value ? 'block' : 'none';
        });
    }

    updateRecipientFields();

    if (sendTo) {
        sendTo.addEventListener('change', updateRecipientFields);
    }

    if (messagesList) {
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    setTimeout(function () {
        if (refreshNotice) {
            refreshNotice.classList.add('is-visible');
        }
    }, 5000);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sendTo = document.getElementById('sendTo');
    const fields = document.querySelectorAll('.recipient-field');
    const refreshNotice = document.getElementById('adminRefreshNotice');
    const messagesList = document.getElementById('adminMessagesList');

    function updateRecipientFields() {
        if (!sendTo) return;

        const value = sendTo.value;

        fields.forEach(field => {
            field.style.display = field.dataset.recipient === value ? 'block' : 'none';
        });
    }

    updateRecipientFields();

    if (sendTo) {
        sendTo.addEventListener('change', updateRecipientFields);
    }

    if (messagesList) {
        messagesList.scrollTop = messagesList.scrollHeight;
    }

    setupVoiceRecorders();

    setTimeout(function () {
        if (refreshNotice) {
            refreshNotice.classList.add('is-visible');
        }
    }, 5000);
});

function setupVoiceRecorders() {
    const recorders = document.querySelectorAll('.voice-recorder');

    recorders.forEach(wrapper => {
        const inputId = wrapper.dataset.target;
        const fileInput = document.getElementById(inputId);

        if (!fileInput) return;

        const recordBtn = wrapper.querySelector('[data-action="record"]');
        const stopBtn = wrapper.querySelector('[data-action="stop"]');
        const clearBtn = wrapper.querySelector('[data-action="clear"]');
        const status = wrapper.querySelector('.voice-status');
        const preview = wrapper.querySelector('.voice-preview');

        let mediaRecorder = null;
        let chunks = [];
        let stream = null;

        function getRecordingMimeType() {
            if (!window.MediaRecorder) {
                return '';
            }

            const types = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg;codecs=opus',
                'audio/ogg',
                'audio/mp4'
            ];

            return types.find(type => MediaRecorder.isTypeSupported(type)) || '';
        }

        function getExtension(mimeType) {
            if (mimeType.includes('ogg')) return 'ogg';
            if (mimeType.includes('mp4')) return 'm4a';
            return 'webm';
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function setPreviewFromFile(file) {
            if (!file) {
                preview.removeAttribute('src');
                preview.style.display = 'none';
                status.textContent = 'Aucun message vocal';
                clearBtn.disabled = true;
                return;
            }

            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            status.textContent = file.name;
            clearBtn.disabled = false;
        }

        function clearVoice() {
            fileInput.value = '';
            setPreviewFromFile(null);
        }

        fileInput.addEventListener('change', function () {
            setPreviewFromFile(fileInput.files[0] ?? null);
        });

        clearBtn.addEventListener('click', clearVoice);

        if (!navigator.mediaDevices || !window.MediaRecorder) {
            recordBtn.disabled = true;
            status.textContent = 'Enregistrement non supporté. Importez un fichier audio.';
            return;
        }

        recordBtn.addEventListener('click', async function () {
            try {
                clearVoice();

                stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });

                chunks = [];

                const mimeType = getRecordingMimeType();

                mediaRecorder = mimeType
                    ? new MediaRecorder(stream, { mimeType })
                    : new MediaRecorder(stream);

                mediaRecorder.addEventListener('dataavailable', function (event) {
                    if (event.data && event.data.size > 0) {
                        chunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', function () {
                    const finalMimeType = mimeType || 'audio/webm';
                    const extension = getExtension(finalMimeType);

                    const blob = new Blob(chunks, {
                        type: finalMimeType
                    });

                    const file = new File(
                        [blob],
                        'voice-message-' + Date.now() + '.' + extension,
                        { type: finalMimeType }
                    );

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    fileInput.files = transfer.files;

                    setPreviewFromFile(file);
                    stopStream();

                    recordBtn.disabled = false;
                    stopBtn.disabled = true;
                    status.textContent = 'Message vocal prêt à envoyer';
                });

                mediaRecorder.start();

                recordBtn.disabled = true;
                stopBtn.disabled = false;
                clearBtn.disabled = true;
                status.textContent = 'Enregistrement en cours...';

            } catch (error) {
                stopStream();

                recordBtn.disabled = false;
                stopBtn.disabled = true;
                status.textContent = 'Micro non disponible ou permission refusée.';
            }
        });

        stopBtn.addEventListener('click', function () {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        });
    });
}
</script>

@endsection
