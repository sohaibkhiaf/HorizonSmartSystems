@extends('admin.layouts.admin')

@section('title', 'Services clients')
@section('page_title', 'Services clients')
@section('page_subtitle', 'Services, licences et liens visibles par les clients')

@section('style')
<style>
    .header{display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:16px}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}.container,.filters{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}.filters{margin-bottom:16px}.filter-grid{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end}.group label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px;font:inherit}.table{width:100%;border-collapse:collapse}.table th{text-align:left;background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;padding:12px}.table td{border-top:1px solid #eef2f7;padding:13px;font-size:14px}.badge{border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}.active{background:#ecfdf5;color:#166534}.maintenance{background:#eff6ff;color:#1d4ed8}.expired,.suspended{background:#fef2f2;color:#991b1b}.empty{text-align:center;color:#64748b;padding:24px}.actions{display:flex;gap:8px}@media(max-width:760px){.header{align-items:flex-start;flex-direction:column}.filter-grid{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
</style>
@endsection

@section('content')
@php($labels = ['active'=>'Actif','maintenance'=>'Maintenance','expired'=>'Expire','suspended'=>'Suspendu'])
<div class="header"><strong>{{ $services->total() }} service(s)</strong><a class="btn btn-main" href="{{ route('admin.customer_services.create') }}"><i class="fas fa-plus"></i> Ajouter un service</a></div>
<form method="GET" class="filters">
    <div class="filter-grid">
        <div class="group">
            <label>Client</label>
            <select name="customer_id">
                <option value="">Tous les clients</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(($filters['customer_id'] ?? '') == $customer->id)>{{ $customer->name }}</option>
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
        <div class="actions"><button class="btn btn-main">Filtrer</button><a class="btn btn-light" href="{{ route('admin.customer_services.index') }}">Reset</a></div>
    </div>
</form>
<div class="container">
    <table class="table"><thead><tr><th>Client</th><th>Service</th><th>Expiration</th><th>Licence</th><th>Statut</th><th>Action</th></tr></thead><tbody>
    @forelse($services as $service)
        <tr><td><strong>{{ $service->user->name ?? 'Client supprime' }}</strong><br><small>{{ $service->user->email ?? '' }}</small></td><td>{{ $service->name }}</td><td>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : '-' }}</td><td>{{ $service->license_key ?: '-' }}</td><td><span class="badge {{ $service->status }}">{{ $labels[$service->status] ?? $service->status }}</span></td><td><a class="btn btn-light" href="{{ route('admin.customer_services.edit', $service) }}"><i class="fas fa-pen"></i> Modifier</a></td></tr>
    @empty
        <tr><td colspan="6" class="empty">Aucun service client.</td></tr>
    @endforelse
    </tbody></table>
    <div style="margin-top:16px;">{{ $services->links() }}</div>
</div>
@endsection
