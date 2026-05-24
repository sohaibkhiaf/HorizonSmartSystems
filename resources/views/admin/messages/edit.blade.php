@extends('admin.layouts.app')

@section('title', 'Edit Message')
@section('page_title', 'Edit Message')
@section('page_subtitle', 'Review and update message status')

@section('content')

<div class="content-card">

    <div class="message-details">
        <h3>{{ $message->subject ?: 'No subject' }}</h3>

        <div class="details-grid">
            <div>
                <strong>Name:</strong>
                <span>{{ $message->name }}</span>
            </div>

            <div>
                <strong>Email:</strong>
                <span>{{ $message->email ?: '-' }}</span>
            </div>

            <div>
                <strong>Phone:</strong>
                <span>{{ $message->phone ?: '-' }}</span>
            </div>

            <div>
                <strong>Date:</strong>
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
                    <span>Seen</span>
                </label>
            </div>

            <div class="form-group full">
                <label>Admin Note</label>
                <textarea name="admin_note" rows="5">{{ old('admin_note', $message->admin_note) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                Update Message
            </button>

            <a href="{{ route('admin.messages.waiting') }}" class="btn-secondary">
                Cancel
            </a>
        </div>
    </form>

</div>

@endsection

