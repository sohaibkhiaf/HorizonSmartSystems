@extends('admin.layouts.app-admin')

@section('title', 'Commandes clients')
@section('page_title', 'Commandes clients')
@section('page_subtitle', 'Commandes visibles dans les espaces clients')

@section('style')
<style>
    .header{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:16px}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700;border:0;cursor:pointer}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}.container,.filters{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}.filters{margin-bottom:16px}.filter-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;align-items:end}.group label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}.group input,.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px;font:inherit}.table{width:100%;border-collapse:collapse}.table th{text-align:left;background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;padding:12px}.table td{border-top:1px solid #eef2f7;padding:13px;font-size:14px}.badge{border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}.completed{background:#ecfdf5;color:#166534}.pending,.processing{background:#fffbeb;color:#92400e}.cancelled{background:#fef2f2;color:#991b1b}.empty{text-align:center;color:#64748b;padding:24px}.actions{display:flex;gap:8px}@media(max-width:1000px){.filter-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:760px){.header{align-items:flex-start;flex-direction:column}.filter-grid{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
</style>
@endsection

@section('content')
@php($labels = ['pending'=>'En attente','processing'=>'En cours','completed'=>'Terminee','cancelled'=>'Annulee'])
<div class="header">
    <strong>{{ $orders->total() }} commande(s)</strong>
    <a class="btn btn-main" href="{{ route('admin.client-orders.create') }}"><i class="fas fa-plus"></i> Ajouter une commande</a>
</div>
<form method="GET" class="filters">
    <div class="filter-grid">
        <div class="group">
            <label>Client</label>
            <select name="client_id">
                <option value="">Tous les clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @selected(($filters['client_id'] ?? '') == $client->id)>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="group">
            <label>Statut</label>
            <select name="status">
                <option value="">Tous les statuts</option>
                @foreach($labels as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="group"><label>Date debut</label><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
        <div class="group"><label>Date fin</label><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
        <div class="actions">
            <button class="btn btn-main" type="submit">Filtrer</button>
            <a class="btn btn-light" href="{{ route('admin.client-orders.index') }}">Reset</a>
        </div>
    </div>
</form>
<div class="container">
    <table class="table">
        <thead><tr><th>Reference</th><th>Client</th><th>Commande</th><th>Prix</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td><strong>{{ $order->user->name ?? 'Client supprime' }}</strong><br><small>{{ $order->user->email ?? '' }}</small></td>
                <td>{{ $order->name }}</td>
                <td>{{ number_format($order->price, 0, ',', ' ') }} DA</td>
                <td><span class="badge {{ $order->status }}">{{ $labels[$order->status] ?? $order->status }}</span></td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td><a class="btn btn-light" href="{{ route('admin.client-orders.edit', $order) }}"><i class="fas fa-pen"></i> Modifier</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Aucune commande client.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:16px;">{{ $orders->links() }}</div>
</div>
@endsection
