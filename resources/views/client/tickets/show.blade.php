@extends('client.layouts.client')

@section('title', 'Conversation support')
@section('page_title', 'Conversation support')
@section('page_subtitle', 'Messages et reponses associes a votre ticket')

@section('style')
<style>
    .ticket-layout{display:grid;grid-template-columns:1fr 340px;gap:18px}.panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px}
    .message{border:1px solid #eef2f7;border-radius:8px;padding:14px;margin-bottom:12px}.message.admin{background:#eff6ff}.message.client{background:#f9fafb}.message h4{margin:0 0 6px}.muted{color:#6b7280;font-size:13px;margin:0}.message p{white-space:pre-line;margin:10px 0 0}
    .badge{display:inline-flex;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}.badge.open{background:#eff6ff;color:#1d4ed8}.badge.waiting{background:#fffbeb;color:#92400e}.badge.answered{background:#ecfdf5;color:#166534}.badge.closed{background:#f3f4f6;color:#374151}
    textarea{width:100%;min-height:150px;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit;resize:vertical}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 13px;font-size:13px;font-weight:700;border:0;cursor:pointer}.btn-primary{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}
    @media(max-width:900px){.ticket-layout{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@php
    $labels = ['open' => 'Ouvert', 'waiting' => 'En attente', 'answered' => 'Repondu', 'closed' => 'Ferme'];
@endphp
<div class="ticket-layout">
    <section class="panel">
        <div class="message client">
            <h4>{{ $ticket->subject }}</h4>
            <p class="muted">Message initial - {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            <p>{{ $ticket->message }}</p>
        </div>

        @foreach($ticket->replies as $reply)
            <div class="message {{ $reply->author_type === 'admin' ? 'admin' : 'client' }}">
                <h4>{{ $reply->author_type === 'admin' ? 'Equipe HSS' : 'Vous' }}</h4>
                <p class="muted">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                <p>{{ $reply->message }}</p>
            </div>
        @endforeach
    </section>

    <aside class="panel">
        <h3 style="margin-top:0;">Statut du ticket</h3>
        <span class="badge {{ $ticket->status }}">{{ $labels[$ticket->status] ?? $ticket->status }}</span>
        <p class="muted" style="margin-top:12px;">Reference #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</p>

        <form method="POST" action="{{ route('client.tickets.reply', $ticket->id) }}" style="margin-top:18px;">
            @csrf
            <label style="display:block;font-weight:700;margin-bottom:7px;">Ajouter une reponse</label>
            <textarea name="message" placeholder="Completez votre demande ou repondez a l'equipe..." required>{{ old('message') }}</textarea>
            <button type="submit" class="btn btn-primary" style="margin-top:10px;"><i class="fas fa-paper-plane"></i> Repondre</button>
        </form>

        <div style="margin-top:14px;">
            <a class="btn btn-light" href="{{ route('client.tickets.index') }}">Retour aux tickets</a>
        </div>
    </aside>
</div>
@endsection
