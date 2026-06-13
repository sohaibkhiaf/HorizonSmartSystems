@extends('admin.layouts.admin')

@section('title', 'Ajouter un service client')
@section('page_title', 'Ajouter un service client')
@section('page_subtitle', 'Publier un acces, une licence ou un lien de telechargement')

@section('style')
<style>
    .panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;max-width:900px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.group label{display:block;font-weight:700;margin-bottom:7px}.group input,.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}.full{grid-column:1/-1}.actions{display:flex;gap:10px;margin-top:18px}.btn{border:0;border-radius:8px;padding:10px 13px;font-weight:700;cursor:pointer}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}@media(max-width:760px){.grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="panel"><form method="POST" action="{{ route('admin.customer_services.store') }}">@csrf
    <div class="grid">
        <div class="group full"><label>Client</label><select name="user_id" required><option value="">Selectionner un client</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('user_id') == $customer->id)>{{ $customer->name }} - {{ $customer->email }}</option>@endforeach</select></div>
        <div class="group"><label>Nom du service</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="group"><label>Date d'expiration</label><input type="date" name="expires_at" value="{{ old('expires_at') }}"></div>
        <div class="group"><label>Statut</label><select name="status" required><option value="active">Actif</option><option value="maintenance">Maintenance</option><option value="expired">Expire</option><option value="suspended">Suspendu</option></select></div>
        <div class="group"><label>Cle de licence</label><input name="license_key" value="{{ old('license_key') }}"></div>
        <div class="group full"><label>Lien de telechargement</label><input type="url" name="download_link" value="{{ old('download_link') }}" placeholder="https://..."></div>
    </div>
    <div class="actions"><button class="btn btn-main">Enregistrer</button><a class="btn btn-light" href="{{ route('admin.customer_services.index') }}">Annuler</a></div>
</form></div>
@endsection
