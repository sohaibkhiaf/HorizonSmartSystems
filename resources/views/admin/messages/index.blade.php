@extends('admin.layouts.app')

@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', 'Manage contact messages')

@section('content')

<div class="sub-tabs">
    <a href="{{ route('admin.messages.waiting') }}" class="{{ !$seen ? 'active' : '' }}">
        Waiting Messages
    </a>

    <a href="{{ route('admin.messages.seen') }}" class="{{ $seen ? 'active' : '' }}">
        Seen Messages
    </a>
</div>

<div class="cards-grid">

    @forelse($messages as $message)
        <div class="message-card">

            <div class="message-header">
                <div>
                    <h3>{{ $message->subject ?: 'No subject' }}</h3>
                    <p>{{ $message->name }}</p>
                </div>

                @if(!$message->seen)
                    <span class="badge warning">Waiting</span>
                @else
                    <span class="badge success">Seen</span>
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
                    Edit
                </a>

                @if(!$message->seen)
                    <form method="POST" action="{{ route('admin.messages.markSeen', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn success-action">
                            <i class="fas fa-check"></i>
                            Seen
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.messages.markWaiting', $message) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="action-btn warning-action">
                            <i class="fas fa-clock"></i>
                            Waiting
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="action-btn delete">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="empty-box">
            No messages found.
        </div>
    @endforelse

</div>

<div class="pagination-box">
    {{ $messages->links() }}
</div>

@endsection

