
@extends('admin.layouts.admin')

@section('page_title', 'Portail client')
@section('page_subtitle', $user->name)

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/customer_portals/show.css') }}">
@endsection

@section('content')

@if(session('success'))
    <div class="flash-success">
        {{ session('success') }}
    </div>
@endif

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
            <form method="POST" action="{{ route('admin.customer_portals.demos.store', $user->id) }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Nom de la démo <span class="required">*</span></label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Ex: Wili Management System"
                           required>
                </div>

                <div class="form-group">
                    <label>Lien de la démo <span class="required">*</span></label>
                    <input type="url"
                           name="demo_link"
                           value="{{ old('demo_link') }}"
                           placeholder="https://demo.example.com"
                           required>
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

        @forelse($demos as $demo)
            <form method="POST"
                  action="{{ route('admin.customer_portals.demos.update', $demo->id) }}"
                  class="demo-form">
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

                <label class="checkbox-line">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ $demo->is_active ? 'checked' : '' }}>
                    Active
                </label>

                <div class="demo-actions">
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
        @empty
            <p class="empty">Aucune démo ajoutée pour ce client.</p>
        @endforelse

    </div>
</div>

<div class="container chat-container">
    <div class="header">
        <h4>Chat avec le client</h4>
    </div>

    <div class="body">

        <div class="chat-box">
            @forelse($messages as $message)
                <div class="chat-message {{ $message->sender_type === 'admin' ? 'admin' : 'customer' }}">
                    <div class="message-content">
                        <p>{{ $message->message }}</p>
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
              class="chat-form">
            @csrf

            <textarea name="message"
                      placeholder="Écrire une réponse au client..."
                      required>{{ old('message') }}</textarea>

            <button type="submit" class="btn-primary">
                Envoyer
            </button>
        </form>

    </div>
</div>

@endsection

