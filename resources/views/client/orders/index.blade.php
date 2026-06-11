@extends('client.layouts.client')

@section('title', 'Mes commandes')
@section('page_title', 'Mes commandes')
@section('page_subtitle', 'Historique complet des achats affectes a votre compte')

@section('style')
<style>
    .toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:16px}
    .summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:18px}
    .card,.panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}
    .card span{color:#6b7280;font-size:13px}.card strong{display:block;font-size:24px;margin-top:6px}
    .table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden}
    .table th{text-align:left;background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;padding:12px}
    .table td{padding:14px 12px;border-top:1px solid #eef2f7;font-size:14px}
    .badge{border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}
    .badge.completed{background:#ecfdf5;color:#166534}.badge.pending,.badge.processing{background:#fffbeb;color:#92400e}.badge.cancelled{background:#fef2f2;color:#991b1b}
    .btn{display:inline-flex;gap:8px;align-items:center;background:#2563eb;color:#fff;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:700}
    .empty{background:#fff;border:1px dashed #cbd5e1;border-radius:8px;padding:30px;text-align:center;color:#64748b}
    @media(max-width:760px){.summary{grid-template-columns:1fr}.toolbar{align-items:flex-start;flex-direction:column}.table{display:block;overflow-x:auto}}
</style>
@endsection

@section('content')
@php
    $labels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Terminee', 'cancelled' => 'Annulee'];
@endphp

<div class="toolbar">
    <div>
        <strong>{{ $orders->count() }} commande(s)</strong>
        <p style="margin:4px 0 0;color:#6b7280;">Suivez le traitement et les details de chaque achat.</p>
    </div>
</div>

<div class="summary">
    <div class="card"><span>Total depense</span><strong>{{ number_format($orders->sum('price'), 0, ',', ' ') }} DA</strong></div>
    <div class="card"><span>Commandes terminees</span><strong>{{ $orders->where('status', 'completed')->count() }}</strong></div>
    <div class="card"><span>En cours ou attente</span><strong>{{ $orders->whereIn('status', ['pending', 'processing'])->count() }}</strong></div>
</div>

@if($orders->count())
    <table class="table">
        <thead>
            <tr><th>Reference</th><th>Produit ou service</th><th>Prix</th><th>Statut</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td><strong>{{ $order->name }}</strong></td>
                <td>{{ number_format($order->price, 0, ',', ' ') }} DA</td>
                <td><span class="badge {{ $order->status }}">{{ $labels[$order->status] ?? $order->status }}</span></td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td><a class="btn" href="{{ route('client.orders.show', $order->id) }}"><i class="fas fa-eye"></i> Details</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="empty">Aucune commande n'est encore rattachee a votre espace client.</div>
@endif
@endsection
