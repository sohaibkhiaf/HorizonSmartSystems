@extends('admin.layouts.app-admin')

@section('title', 'Repondre au ticket')
@section('page_title', 'Repondre au ticket')
@section('page_subtitle', 'Envoyer une reponse visible dans l\'espace client')

@section('style')
<style>
    .layout{display:grid;grid-template-columns:1fr 360px;gap:18px}.panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px}.message{border:1px solid #eef2f7;border-radius:8px;padding:14px;margin-bottom:12px}.message.admin{background:#eff6ff}.message.client{background:#f9fafb}.message h4{margin:0 0 6px}.muted{color:#6b7280;font-size:13px;margin:0}.message p{white-space:pre-line;margin:10px 0 0}.group label{display:block;font-weight:700;margin-bottom:7px}.group textarea,.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}.group textarea{min-height:170px;resize:vertical}.btn{border:0;border-radius:8px;padding:10px 13px;font-weight:700;cursor:pointer}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827;display:inline-flex;margin-top:10px}@media(max-width:900px){.layout{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="layout">
    <section class="panel">
        <div class="message client">
            <h4>{{ $ticket->user->name ?? 'Client' }} - {{ $ticket->subject }}</h4>
            <p class="muted">Message initial du {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            <p>{{ $ticket->message }}</p>
        </div>
        @foreach($ticket->replies as $reply)
            <div class="message {{ $reply->author_type === 'admin' ? 'admin' : 'client' }}">
                <h4>{{ $reply->author_type === 'admin' ? 'Equipe HSS' : ($ticket->user->name ?? 'Client') }}</h4>
                <p class="muted">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                <p>{{ $reply->message }}</p>
            </div>
        @endforeach
    </section>
    <aside class="panel">
        <h3 style="margin-top:0;">Reponse admin</h3>
        <form method="POST" action="{{ route('admin.client-tickets.reply', $ticket) }}">
            @csrf
            <div class="group"><label>Statut apres reponse</label><select name="status" required><option value="answered" @selected($ticket->status === 'answered')>Repondu</option><option value="waiting" @selected($ticket->status === 'waiting')>En attente</option><option value="open" @selected($ticket->status === 'open')>Ouvert</option><option value="closed" @selected($ticket->status === 'closed')>Ferme</option></select></div>
            <div class="group" style="margin-top:14px;"><label>Message</label><textarea name="message" required>{{ old('message') }}</textarea></div>
            <button class="btn btn-main" style="margin-top:12px;">Envoyer la reponse</button>
        </form>
        <a class="btn-light" href="{{ route('admin.client-tickets.index') }}">Retour aux tickets</a>
    </aside>
</div>
@endsection
