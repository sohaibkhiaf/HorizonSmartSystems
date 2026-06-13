@extends('admin.layouts.admin')

@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', 'Gérer les messages de contact')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/messages/index.css') }}">
@endsection

@section('content')

<div class="tabs">
    <a href="{{ route('admin.messages.waiting') }}" class="{{ !$is_seen ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        Messages en attente
    </a>

    <a href="{{ route('admin.messages.seen') }}" class="{{ $is_seen ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Messages consultés
    </a>
</div>

<div class="messages-grid">

    @forelse($messages as $message)
        <div class="message-card">

            <div class="message-top">
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>

                <div class="message-main">
                    <div class="message-title-row">
                        <h3>{{ $message->subject ?: 'Sans objet' }}</h3>

                        @if(!$message->is_seen)
                            <span class="badge warning">En attente</span>
                        @else
                            <span class="badge success">Consulté</span>
                        @endif
                    </div>

                    <p class="message-sender">
                        {{ $message->full_name }}
                    </p>
                </div>
            </div>

            <div class="message-info">
                @if($message->email)
                    <div class="info-row">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $message->email }}</span>
                    </div>
                @endif

                @if($message->phone)
                    <div class="info-row">
                        <i class="fas fa-phone"></i>
                        <span>{{ $message->phone }}</span>
                    </div>
                @endif
            </div>

            <div class="message-preview">
                {{ $message->message }}
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.messages.edit', $message) }}" class="action-btn edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </a>

                @if(!$message->is_seen)
                    <form method="POST" action="{{ route('admin.messages.markSeen', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn success">
                            <i class="fas fa-check"></i>
                            Consulté
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.messages.markWaiting', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn warning">
                            <i class="fas fa-clock"></i>
                            En attente
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Supprimer ce message ?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="action-btn delete">
                        <i class="fas fa-trash"></i>
                        Supprimer
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>

            <h3>Aucun message trouvé</h3>

            <p>
                Il n’y a actuellement aucun message dans cette section.
            </p>

            @if($is_seen)
                <a href="{{ route('admin.messages.waiting') }}" class="empty-link">
                    Voir les messages en attente
                </a>
            @else
                <a href="{{ route('admin.messages.seen') }}" class="empty-link">
                    Voir les messages consultés
                </a>
            @endif
        </div>
    @endforelse

</div>

@if($messages->hasPages())
    <div class="navigator-container">
        @if ($messages->currentPage() > 1)
            <a href="{{ $messages->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $messages->url($messages->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $messages->lastPage(); $i++)
            <a href="{{ $messages->url($i) }}" class="{{ $messages->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($messages->currentPage() < $messages->lastPage())
            <a href="{{ $messages->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $messages->url($messages->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection
