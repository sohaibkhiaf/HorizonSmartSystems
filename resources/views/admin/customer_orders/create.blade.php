@extends('admin.layouts.admin')

@section('title', 'Ajouter une commande client')
@section('page_title', 'Ajouter une commande client')
@section('page_subtitle', 'Affecter une commande a un compte client')

@section('style')
<style>
    .panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;max-width:900px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.group label{display:block;font-weight:700;margin-bottom:7px}.group input,.group select{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}.full{grid-column:1/-1}.actions{display:flex;gap:10px;margin-top:18px}.btn{border:0;border-radius:8px;padding:10px 13px;font-weight:700;cursor:pointer}.btn-main{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}@media(max-width:760px){.grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('admin.customer_orders.store') }}">
        @csrf
        <div class="grid">
            <div class="group full">
                <label>Client</label>
                <select name="user_id" required>
                    <option value="">Selectionner un client</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('user_id') == $customer->id)>{{ $customer->name }} - {{ $customer->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="group"><label>Nom de la commande</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="group"><label>Prix</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required></div>
            <div class="group"><label>Statut</label><select name="status" required><option value="pending">En attente</option><option value="processing">En cours</option><option value="completed">Terminee</option><option value="cancelled">Annulee</option></select></div>
        </div>
        <div class="actions"><button class="btn btn-main">Enregistrer</button><a class="btn btn-light" href="{{ route('admin.customer_orders.index') }}">Annuler</a></div>
    </form>
</div>
@endsection
