@extends('client.layouts.client')

@section('title', 'Support')
@section('page_title', 'Support')
@section('page_subtitle', 'Suivez vos demandes et les reponses de l\'equipe')

@section('style')
<style>
    .toolbar{display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:18px}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700;border:0;cursor:pointer}
    .btn-primary{background:#2563eb;color:#fff}.ticket-list{display:grid;gap:12px}.ticket{display:grid;grid-template-columns:1fr auto auto;gap:14px;align-items:center;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;color:#111827}
    .ticket h3{margin:0;font-size:17px}.muted{color:#6b7280;font-size:13px;margin:5px 0 0}.badge{border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
    .badge.open{background:#eff6ff;color:#1d4ed8}.badge.waiting{background:#fffbeb;color:#92400e}.badge.answered{background:#ecfdf5;color:#166534}.badge.closed{background:#f3f4f6;color:#374151}
    .counter{background:#f9fafb;border:1px solid #eef2f7;border-radius:8px;padding:9px 11px;font-size:13px;color:#374151}.empty{background:#fff;border:1px dashed #cbd5e1;border-radius:8px;padding:30px;text-align:center;color:#64748b}
    @media(max-width:760px){.toolbar,.ticket{grid-template-columns:1fr;align-items:flex-start}}
</style>
@endsection

@section('content')
@php
    $labels = ['open' => 'Ouvert', 'waiting' => 'En attente', 'answered' => 'Repondu', 'closed' => 'Ferme'];
@endphp
<div class="toolbar">
    <div>
        <strong>{{ $tickets->count() }} ticket(s)</strong>
        <p class="muted">Toutes vos demandes de support sont centralisees ici.</p>
    </div>
    <a href="{{ route('client.tickets.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau ticket</a>
</div>

@if($tickets->count())
    <div class="ticket-list">
        @foreach($tickets as $ticket)
            <a class="ticket" href="{{ route('client.tickets.show', $ticket->id) }}">
                <div>
                    <h3>{{ $ticket->subject }}</h3>
                    <p class="muted">Cree le {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="badge {{ $ticket->status }}">{{ $labels[$ticket->status] ?? $ticket->status }}</span>
                <span class="counter">{{ $ticket->replies_count }} reponse(s)</span>
            </a>
        @endforeach
    </div>
@else
    <div class="empty">Aucun ticket pour le moment. Vous pouvez ouvrir une demande si vous avez besoin d'assistance.</div>
@endif
@endsection
