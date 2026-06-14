@extends('admin.layouts.admin')

@section('title', 'Modifier le message')
@section('page_title', 'Modifier le message')
@section('page_subtitle', 'Consulter et mettre à jour le statut du message')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_messages/edit.css') }}">
@endsection

@section('content')

<div class="container">

    <div class="message-panel">

        <div class="message-top">
            <div class="message-avatar">
                <i class="fas fa-envelope-open-text"></i>
            </div>

            <div class="message-heading">
                <div class="message-title-row">
                    <h3>{{ $message->subject ?: 'Sans objet' }}</h3>

                    @if($message->is_seen)
                        <span class="badge success">Consulté</span>
                    @else
                        <span class="badge warning">En attente</span>
                    @endif
                </div>

                <p>
                    Message envoyé par <strong>{{ $message->full_name }}</strong>
                </p>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <i class="fas fa-user"></i>
                <div>
                    <strong>Nom complet</strong>
                    <span>{{ $message->full_name }}</span>
                </div>
            </div>

            <div class="detail-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>E-mail</strong>
                    <span>{{ $message->email ?: 'Non renseigné' }}</span>
                </div>
            </div>

            <div class="detail-item">
                <i class="fas fa-phone"></i>
                <div>
                    <strong>Téléphone</strong>
                    <span>{{ $message->phone ?: 'Non renseigné' }}</span>
                </div>
            </div>

            <div class="detail-item">
                <i class="fas fa-calendar"></i>
                <div>
                    <strong>Date d’envoi</strong>
                    <span>{{ $message->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="message-full">
            <div class="section-label">
                <i class="fas fa-message"></i>
                Message
            </div>

            <p>{{ $message->message }}</p>
        </div>

    </div>

    <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="update-form">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_seen"
                    value="1"
                    {{ old('is_seen', $message->is_seen) ? 'checked' : '' }}>
                    Marquer comme consulté
            </label>

            <div class="form-group full">
                <label>Note de l'administrateur</label>

                <textarea
                    name="admin_note"
                    rows="5"
                    placeholder="Ajouter une note interne : actions prises, suivi nécessaire, remarques importantes...">{{ old('admin_note', $message->admin_note) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                Mettre à jour
            </button>

            <a href="{{ $message->is_seen ? route('admin.messages.seen') : route('admin.messages.waiting') }}" class="btn-secondary">
                Annuler
            </a>
        </div>
    </form>

</div>

@endsection
