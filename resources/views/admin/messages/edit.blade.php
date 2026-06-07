@extends('admin.layouts.app-admin')

@section('title', 'Modifier le message')
@section('page_title', 'Modifier le message')
@section('page_subtitle', 'Consulter et mettre à jour le statut du message')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/messages/edit.css') }}">
@endsection

@section('content')

<div class="container">

    <div class="message-details">

        <h3>{{ $message->subject ?: 'Sans objet' }}</h3>

        <div class="details-grid">
            <div>
                <strong>Nom :</strong>
                <span>{{ $message->name }}</span>
            </div>

            <div>
                <strong>E-mail :</strong>
                <span>{{ $message->email ?: '-' }}</span>
            </div>

            <div>
                <strong>Téléphone :</strong>
                <span>{{ $message->phone ?: '-' }}</span>
            </div>

            <div>
                <strong>Date :</strong>
                <span>{{ $message->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="message-full">
            {{ $message->message }}
        </div>
    </div>

    <form method="POST" action="{{ route('admin.messages.update', $message) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group full">
                <label class="checkbox-row">
                    <input 
                        type="checkbox" 
                        name="is_seen" 
                        value="1"
                        {{ old('is_seen', $message->is_seen ?? true) ? 'checked' : '' }}>
                    <span>Consulté</span>
                </label>
            </div>

            <div class="form-group full">
                <label>Note de l'administrateur</label>
                <textarea name="admin_note" rows="5"
                    placeholder="Ajouter une note interne (visible uniquement par l'administrateur) concernant ce message : actions prises, suivi nécessaire, ou remarques importantes...">{{ old('admin_note', $message->admin_note) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                Mettre à jour le message
            </button>

            <a href="{{ route('admin.messages.waiting') }}" class="btn-secondary">
                Annuler
            </a>
        </div>
    </form>

</div>

@endsection

