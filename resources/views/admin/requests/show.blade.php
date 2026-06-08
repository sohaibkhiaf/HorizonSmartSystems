@extends('admin.layouts.app-admin')

@section('page_title', 'Détails de la demande')
@section('page_subtitle', 'Gestion complète de la requête')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/requests/show.css') }}">
@endsection

@section('content')

<div class="grid">

    {{-- LEFT INFO --}}
    <div class="container">

        <div class="header">
            <h4>Informations client</h4>
        </div>

        <div class="body">

            {{-- CLIENT --}}
            <p class="section-label">Client</p>

            <div class="info-grid">
                <div class="info-block full">
                    <p class="label">Nom complet</p>
                    <p class="value">{{ $request->client_name }}</p>
                </div>
                <div class="info-block">
                    <p class="label">Contact</p>
                    <p class="value">{{ $request->client_contact }}</p>
                </div>
                <div class="info-block">
                    <p class="label">E-mail</p>
                    <p class="value">{{ $request->client_email ?? 'N/A' }}</p>
                </div>
                <div class="info-block full">
                    <p class="label">Adresse</p>
                    <p class="value">{{ $request->client_address ?? 'N/A' }}</p>
                </div>
            </div>


            {{-- PRODUIT --}}
            <p class="section-label">Produit</p>

            <div class="info-grid">
                <div class="info-block">
                    <p class="label">Produit</p>
                    <p class="value">{{ $request->product->name ?? 'N/A' }}</p>
                </div>
                <div class="info-block">
                    <p class="label">Type</p>
                    <p class="value">
                        @if ($request->product->type == 'key')
                            Clé
                        @elseif ($request->product->type == 'account')
                            Compte
                        @elseif ($request->product->type == 'invitation')
                            Invitation
                        @endif
                    </p>
                </div>
                <div class="info-block">
                    <p class="label">Prix unitaire</p>
                    <p class="value">{{ $request->product->price }} DA</p>
                </div>
                <div class="info-block">
                    <p class="label">Quantité</p>
                    <p class="value">{{ $request->quantity }}</p>
                </div>
            </div>


            {{-- COMMANDE --}}
            <p class="section-label">Commande</p>

            <div class="info-grid">
                <div class="info-block">
                    <p class="label">Total</p>
                    <p class="value total">{{ $request->product->price * $request->quantity }} DA</p>
                </div>
                <div class="info-block">
                    <p class="label">Statut actuel</p>
                    @if($request->status === 'pending')
                        <span class="badge muted">En attente</span>
                    @elseif($request->status === 'processing')
                        <span class="badge muted">En cours</span>
                    @elseif($request->status === 'available')
                        <span class="badge success">Disponible</span>
                    @elseif($request->status === 'paid')
                        <span class="badge success">Payé</span>
                    @elseif($request->status === 'delivered')
                        <span class="badge success">Livré</span>
                    @elseif($request->status === 'postponed')
                        <span class="badge muted">Reportée</span>
                    @elseif($request->status === 'canceled')
                        <span class="badge warning">Annulée</span>
                    @endif
                </div>
            </div>

        </div>

    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="container">

        <div class="header">
            <h4>Actions admin</h4>
        </div>

        <div class="body">

            {{-- STATUS FORM --}}
            <form method="POST" action="{{ url('admin/requests/'.$request->id.'/availability') }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>En cours</option>
                        <option value="available" {{ $request->status == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="postponed" {{ $request->status == 'postponed' ? 'selected' : '' }}>Reportée</option>
                        <option value="canceled" {{ $request->status == 'canceled' ? 'selected' : '' }}>Annulée</option>
                        <option value="paid" {{ $request->status == 'paid' ? 'selected' : '' }}>Payée</option>
                        <option value="delivered" {{ $request->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Note admin</label>
                    <textarea name="admin_note" class="form-control" placeholder="Note pour l'opérateur">{{ $request->admin_note }}</textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Mettre à jour
                </button>

            </form>

        </div>

    </div>

</div>

@endsection

