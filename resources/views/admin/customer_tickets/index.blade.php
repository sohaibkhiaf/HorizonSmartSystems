@extends('admin.layouts.admin')

@section('title', 'Tickets clients')
@section('page_title', 'Tickets clients')
@section('page_subtitle', 'Demandes de support envoyees depuis l\'espace client')

@section('style')
<style>
    .container{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}.table{width:100%;border-collapse:collapse}.table th{text-align:left;background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;padding:12px}.table td{border-top:1px solid #eef2f7;padding:13px;font-size:14px}.badge{border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}.open{background:#eff6ff;color:#1d4ed8}.waiting{background:#fffbeb;color:#92400e}.answered{background:#ecfdf5;color:#166534}.closed{background:#f3f4f6;color:#374151}.btn{display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:#fff;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:700}.empty{text-align:center;color:#64748b;padding:24px}@media(max-width:760px){.table{display:block;overflow-x:auto}}
</style>
@endsection

@section('content')
@php($labels = ['open'=>'Ouvert','waiting'=>'En attente','answered'=>'Repondu','closed'=>'Ferme'])
<div class="container">
    <table class="table"><thead><tr><th>Client</th><th>Sujet</th><th>Reponses</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead><tbody>
    @forelse($tickets as $ticket)
        <tr><td><strong>{{ $ticket->user->name ?? 'Client supprime' }}</strong><br><small>{{ $ticket->user->email ?? '' }}</small></td><td>{{ $ticket->subject }}</td><td>{{ $ticket->replies->count() }}</td><td><span class="badge {{ $ticket->status }}">{{ $labels[$ticket->status] ?? $ticket->status }}</span></td><td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td><td><a class="btn" href="{{ route('admin.customer_tickets.show', $ticket) }}"><i class="fas fa-reply"></i> Repondre</a></td></tr>
    @empty
        <tr><td colspan="6" class="empty">Aucun ticket client.</td></tr>
    @endforelse
    </tbody></table>
    <div style="margin-top:16px;">{{ $tickets->links() }}</div>
</div>
@endsection
