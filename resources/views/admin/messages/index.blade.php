@extends('admin.layouts.app-admin')

@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', 'Gérer les messages de contact')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/messages/index.css') }}">
@endsection

@section('content')

<div class="sub-tabs">
    <a href="{{ route('admin.messages.waiting') }}" class="{{ !$is_seen ? 'active' : '' }}">
        Messages en attente
    </a>

    <a href="{{ route('admin.messages.seen') }}" class="{{ $is_seen ? 'active' : '' }}">
        Messages consultés
    </a>
</div>

<div class="cards-grid">

    @forelse($messages as $message)
        <div class="message-card">

            <div class="message-header">
                <div>
                    <h3>{{ $message->subject ?: 'Sans objet' }}</h3>
                    <p>{{ $message->name }}</p>
                </div>

                @if(!$message->is_seen)
                    <span class="badge warning">En attente</span>
                @else
                    <span class="badge success">Consulté</span>
                @endif
            </div>

            <div class="message-info">
                @if($message->email)
                    <span><i class="fas fa-envelope"></i> {{ $message->email }}</span>
                @endif

                @if($message->phone)
                    <span><i class="fas fa-phone"></i> {{ $message->phone }}</span>
                @endif
            </div>

            <p class="message-text">
                {{ Str::limit($message->message, 150) }}
            </p>

            <div class="card-actions">
                <a href="{{ route('admin.messages.edit', $message) }}" class="action-btn edit">
                    <i class="fas fa-pen"></i>
                    Modifier
                </a>

                @if(!$message->is_seen)
                    <form method="POST" action="{{ route('admin.messages.markSeen', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn success-action">
                            <i class="fas fa-check"></i>
                            Consulté
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.messages.markWaiting', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn warning-action">
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
        <div class="empty-box">
            Aucun message trouvé.
        </div>
    @endforelse

</div>

<div class="navigator-container">
    @if ($messages->currentPage() > 1)
        <a href="{{$messages->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @else
        <a href="{{$messages->url($messages->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @endif

    @for ($i = 1; $i <= $messages->lastPage(); $i++)
        @if ($messages->currentPage() == $i)
            <a href="{{$messages->url($i)}}" class="active">{{$i}}</a>
        @else
            <a href="{{$messages->url($i)}}">{{$i}}</a>
        @endif
    @endfor

    @if ($messages->currentPage() < $messages->lastPage())
        <a href="{{$messages->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @else
        <a href="{{$messages->url($messages->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @endif
</div>

@endsection

