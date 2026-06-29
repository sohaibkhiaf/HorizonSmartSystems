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

        <div class="messages-column">
            <div class="container messages-container">
                <div class="header chat-header">
                    <div>
                        <h4>Messages</h4>
                    </div>
                </div>

                <div class="body chat-body">

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
                                    $messageImageUrl = asset('uploads/' . $message->image);
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

                    <div class="message-controls-card">

                        <form method="POST"
                              action="{{ route('admin.chats.messages.store') }}"
                              class="compose-form chat-form"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="compose-recipients">
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
                            </div>

                            <textarea
                                name="message"
                                placeholder="Écrire un message...">{{ old('message') }}</textarea>

                            <input
                                type="url"
                                name="link"
                                class="chat-link-input"
                                value="{{ old('link') }}"
                                placeholder="Lien optionnel : https://example.com">

                            <div class="chat-attachments">
                                <div class="file-picker" data-file-picker>
                                    <input
                                        type="file"
                                        name="image"
                                        id="adminMessageImage"
                                        accept="image/*"
                                        class="file-picker-input">

                                    <label for="adminMessageImage" class="file-picker-btn">
                                        <i class="fa-solid fa-image"></i>
                                        Image
                                    </label>

                                    <span class="file-picker-name" data-default="Aucune image sélectionnée">
                                        Aucune image sélectionnée
                                    </span>
                                </div>

                                <div class="file-picker" data-file-picker>
                                    <input
                                        type="file"
                                        name="voice_message"
                                        id="adminVoiceMessage"
                                        accept="audio/*,.webm,.ogg,.mp3,.wav,.m4a,.aac,.mp4"
                                        class="file-picker-input">

                                    <label for="adminVoiceMessage" class="file-picker-btn">
                                        <i class="fa-solid fa-file-audio"></i>
                                        Audio
                                    </label>

                                    <span class="file-picker-name" data-default="Aucun audio sélectionné">
                                        Aucun audio sélectionné
                                    </span>
                                </div>
                            </div>

                            <div class="voice-message-group">
                                <div
                                    class="voice-recorder"
                                    data-target="adminVoiceMessage"
                                    data-empty-label="Aucun message vocal"
                                    data-ready-label="Message vocal prêt à envoyer"
                                    data-recording-label="Enregistrement en cours..."
                                    data-not-supported-label="Enregistrement non supporté. Importez un fichier audio."
                                    data-microphone-error-label="Micro non disponible ou permission refusée.">

                                    <div class="voice-actions">
                                        <button type="button" class="voice-record-btn" data-action="record">
                                            <i class="fa-solid fa-microphone"></i>
                                            <span>Enregistrer</span>
                                        </button>

                                        <button type="button" class="voice-stop-btn" data-action="stop" disabled>
                                            <i class="fa-solid fa-stop"></i>
                                            <span>Arrêter</span>
                                        </button>

                                        <button type="button" class="voice-clear-btn" data-action="clear" disabled>
                                            <i class="fa-solid fa-trash"></i>
                                            <span>Supprimer</span>
                                        </button>
                                    </div>

                                    <span class="voice-status">
                                        Aucun message vocal
                                    </span>

                                    <audio controls class="voice-preview"></audio>
                                </div>
                            </div>

                            <button type="submit" class="btn-main compose-submit">
                                <i class="fa-solid fa-paper-plane"></i>
                                Envoyer
                            </button>
                        </form>
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

        <div class="groups-column">
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

    setupFilePickers();
    setupVoiceRecorders();

    setTimeout(function () {
        if (refreshNotice) {
            refreshNotice.classList.add('is-visible');
        }
    }, 5000);
});

function setupFilePickers() {
    const fileInputs = document.querySelectorAll('.file-picker-input');

    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            updateFilePickerName(input, input.files[0] ?? null);
        });
    });
}

function updateFilePickerName(input, file) {
    const picker = input.closest('[data-file-picker]');

    if (!picker) return;

    const name = picker.querySelector('.file-picker-name');

    if (!name) return;

    if (!file) {
        name.textContent = name.dataset.default || '';
        name.removeAttribute('title');
        picker.classList.remove('has-file');
        return;
    }

    name.textContent = file.name;
    name.title = file.name;
    picker.classList.add('has-file');
}

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

        if (!recordBtn || !stopBtn || !clearBtn || !status) return;

        const labels = {
            empty: wrapper.dataset.emptyLabel || 'Aucun message vocal',
            ready: wrapper.dataset.readyLabel || 'Message vocal prêt à envoyer',
            recording: wrapper.dataset.recordingLabel || 'Enregistrement en cours...',
            notSupported: wrapper.dataset.notSupportedLabel || 'Enregistrement non supporté. Importez un fichier audio.',
            microphoneError: wrapper.dataset.microphoneErrorLabel || 'Micro non disponible ou permission refusée.',
        };

        let mediaRecorder = null;
        let chunks = [];
        let stream = null;
        let previewUrl = null;

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

        function resetPreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function hidePreview() {
            if (!preview) return;

            preview.pause();
            preview.removeAttribute('src');
            preview.load();
            preview.classList.remove('is-visible');
        }

        function showPreview(file) {
            if (!preview || !file) return;

            previewUrl = URL.createObjectURL(file);

            preview.pause();
            preview.src = previewUrl;
            preview.preload = 'metadata';
            preview.load();
            preview.classList.add('is-visible');
        }

        function setPreviewFromFile(file, ready = false) {
            resetPreviewUrl();

            if (!file) {
                hidePreview();

                status.textContent = labels.empty;
                clearBtn.disabled = true;
                updateFilePickerName(fileInput, null);

                return;
            }

            showPreview(file);

            status.textContent = ready ? labels.ready : file.name;
            clearBtn.disabled = false;
            updateFilePickerName(fileInput, file);
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
            status.textContent = labels.notSupported;
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

                    setPreviewFromFile(file, true);
                    stopStream();

                    recordBtn.disabled = false;
                    stopBtn.disabled = true;
                });

                mediaRecorder.start();

                recordBtn.disabled = true;
                stopBtn.disabled = false;
                clearBtn.disabled = true;
                status.textContent = labels.recording;

            } catch (error) {
                stopStream();

                recordBtn.disabled = false;
                stopBtn.disabled = true;
                status.textContent = labels.microphoneError;
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
