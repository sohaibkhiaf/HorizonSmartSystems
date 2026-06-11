@extends('admin.layouts.app-admin')

@section('title', 'Modifier un abonnement client')
@section('page_title', 'Modifier un abonnement client')
@section('page_subtitle', 'Mettre a jour le prix, la date de renouvellement et le statut')

@section('style')
<style>
    .panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;max-width:900px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.group label{display:block;font-weight:700;margin-bottom:7px}.group input,.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}.full{grid-column:1/-1}.actions{display:flex;gap:10px;margin-top:18px}.btn{border:0;border-radius:8px;padding:10px 13px;font-weight:700;cursor:pointer}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}@media(max-width:760px){.grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.client-subscriptions.update', $subscription) }}">
        @csrf
        @method('PUT')
        <div class="grid">
            <div class="group full"><label>Client</label><select name="user_id" required>@foreach($clients as $client)<option value="{{ $client->id }}" @selected(old('user_id', $subscription->user_id) == $client->id)>{{ $client->name }} - {{ $client->email }}</option>@endforeach</select></div>
            <div class="group"><label>Nom de l'abonnement</label><input name="name" value="{{ old('name', $subscription->name) }}" required></div>
            <div class="group"><label>Prix</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $subscription->price) }}" required></div>
            <div class="group"><label>Date de renouvellement</label><input type="date" name="renewal_date" value="{{ old('renewal_date', $subscription->renewal_date->format('Y-m-d')) }}" required></div>
            <div class="group">
                <label>Statut</label>
                <select name="status" required>
                    <option value="active" @selected(old('status', $subscription->status) === 'active')>Actif</option>
                    <option value="pending" @selected(old('status', $subscription->status) === 'pending')>En attente</option>
                    <option value="cancelled" @selected(old('status', $subscription->status) === 'cancelled')>Annule</option>
                    <option value="expired" @selected(old('status', $subscription->status) === 'expired')>Expire</option>
                </select>
            </div>
        </div>
        <div class="actions"><button class="btn btn-main">Mettre a jour</button><a class="btn btn-light" href="{{ route('admin.client-subscriptions.index') }}">Annuler</a></div>
    </form>
</div>
@endsection
