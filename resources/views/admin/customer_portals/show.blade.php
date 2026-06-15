@extends('admin.layouts.admin')

@section('page_title', 'Portail client')
@section('page_subtitle', $user->name)

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/customer_portals/show.css') }}">
@endsection

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.customer_portals.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Retour
    </a>
</div>

<div class="grid">

    <div class="container">
        <div class="header">
            <h4>Informations client</h4>
        </div>

        <div class="body">
            <div class="info-grid">
                <div class="info-block">
                    <p class="label">Nom</p>
                    <p class="value">{{ $user->name }}</p>
                </div>

                <div class="info-block">
                    <p class="label">E-mail</p>
                    <p class="value">{{ $user->email }}</p>
                </div>

                <div class="info-block">
                    <p class="label">Téléphone</p>
                    <p class="value">{{ $user->phone ?? 'N/A' }}</p>
                </div>

                <div class="info-block">
                    <p class="label">Entreprise</p>
                    <p class="value">{{ $user->company ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h4>Ajouter une démo</h4>
        </div>

        <div class="body">
            <form method="POST" action="{{ route('admin.customer_portals.demos.store', $user->id) }}" class="form"
                enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Nom de la démo <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Wili Management System" required>
                </div>

                <div class="form-group">
                    <label>Lien de la démo <span class="required">*</span></label>
                    <input type="url" name="demo_link" value="{{ old('demo_link') }}" placeholder="https://demo.example.com" required>
                </div>

                <div class="form-group">
                    <label>Image / capture</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Note admin</label>
                    <textarea name="admin_note" placeholder="Identifiants, accès ou remarques...">{{ old('admin_note') }}</textarea>
                </div>

                <label class="checkbox-line">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Démo active
                </label>

                <button type="submit" class="btn-primary">
                    Ajouter la démo
                </button>
            </form>
        </div>
    </div>

</div>

<div class="container demos-container">
    <div class="header">
        <h4>Démos du client</h4>
    </div>

    <div class="body">
        <div class="demos-grid">
            @forelse($demos as $demo)
                <div class="demo-wrapper">

                    @if($demo->image)
                        <div class="demo-preview">
                            <img src="{{ asset('uploads/' . $demo->image) }}" alt="{{ $demo->name }}">
                        </div>
                    @else
                        <div class="demo-preview empty-preview">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ route('admin.customer_portals.demos.update', $demo->id) }}"
                        class="demo-form"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="name" value="{{ $demo->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Lien</label>
                            <input type="url" name="demo_link" value="{{ $demo->demo_link }}" required>
                        </div>

                        <div class="form-group demo-full-row">
                            <label>Image / capture</label>
                            <input type="file" name="image" accept="image/*">
                        </div>

                        <div class="form-group demo-full-row">
                            <label>Note admin</label>
                            <textarea name="admin_note">{{ $demo->admin_note }}</textarea>
                        </div>

                        <label class="checkbox-line demo-full-row">
                            <input type="checkbox" name="is_active" value="1" {{ $demo->is_active ? 'checked' : '' }}>
                            Active
                        </label>

                        <div class="demo-actions demo-full-row">
                            <a href="{{ $demo->demo_link }}" target="_blank" class="action-btn edit">
                                <i class="fas fa-up-right-from-square"></i>
                                Ouvrir
                            </a>

                            <button type="submit" class="action-btn success">
                                <i class="fas fa-save"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>

                    <form method="POST"
                        action="{{ route('admin.customer_portals.demos.destroy', $demo->id) }}"
                        class="delete-form">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="action-btn delete"
                                onclick="return confirm('Supprimer cette démo ?')">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </form>

                </div>
            @empty
                <p class="empty">Aucune démo ajoutée pour ce client.</p>
            @endforelse
        </div>

    </div>
</div>

<div class="container chat-container">
    <div class="header">
        <h4>Chat avec le client</h4>
    </div>

    <div class="body">

        <div class="chat-box" id="adminChatBox">
            @forelse($messages as $message)
                <div class="chat-message {{ $message->sender_type === 'admin' ? 'admin' : 'customer' }}">
                    <div class="message-content">

                        @if($message->message)
                            <p>{{ $message->message }}</p>
                        @endif

                        @if($message->link)
                            <a href="{{ $message->link }}" target="_blank" class="message-link">
                                {{ $message->link }}
                            </a>
                        @endif

                        @if($message->image)
                            <a href="{{ asset('uploads/' . $message->image) }}" target="_blank">
                                <img src="{{ asset('uploads/' . $message->image) }}" alt="Image message" class="message-image">
                            </a>
                        @endif

                        <span>
                            {{ $message->sender_type === 'admin' ? 'Admin' : $user->name }}
                            ·
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="empty">Aucun message pour le moment.</p>
            @endforelse
        </div>

        <form method="POST"
              action="{{ route('admin.customer_portals.messages.send', $user->id) }}"
              class="chat-form"
              enctype="multipart/form-data">
            @csrf

            <textarea name="message" placeholder="Écrire une réponse au client...">{{ old('message') }}</textarea>

            <input type="url" name="link" placeholder="Lien optionnel" value="{{ old('link') }}">

            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane"></i>
                Envoyer
            </button>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatBox = document.getElementById('adminChatBox');

        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>

@endsection
