

@extends('admin.layouts.app-operator')

@section('page_title', 'Détails de la demande')
@section('page_subtitle', 'Gestion de la requête client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/operator/requests/show.css') }}">
@endsection

@section('content')

<div class="grid">

    {{-- LEFT INFO --}}
    <div class="container">

        <div class="header">
            <h4>Informations client</h4>
        </div>

        <div class="body">

            <div class="info-block">
                <p class="label">Nom</p>
                <p class="value">{{ $request->client_name }}</p>
            </div>

            <div class="info-block">
                <p class="label">Contact</p>
                <p class="value">{{ $request->client_contact }}</p>
            </div>

            <div class="info-block">
                <p class="label">Produit</p>
                <p class="value">{{ $request->product->name ?? 'N/A' }} {{ $request->product->price ?? '' }} DA</p>
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
                @elseif($request->status === 'not_available')
                    <span class="badge warning">Non disponible</span>
                @endif
            </div>

            @foreach ($request->notes as $note )
                @if($note->sender == 'ADMIN')
                    <div class="info-block">
                        <p class="label">Note admin</p>
                        <div class="note-box-admin" style="white-space: pre-line;">{{ $note->content }} ({{ $note->created_at->format('Y-m-d H:i') }})</div>
                    </div>
                @elseif($note->sender == 'OPERATOR')
                    <div class="info-block">
                        <p class="label">Note opérateur</p>
                        <div class="note-box-operator" style="white-space: pre-line;">{{ $note->content }} ({{ $note->created_at->format('Y-m-d H:i') }})</div>
                    </div>
                @endif
                    
            @endforeach

        </div>

    </div>


    {{-- RIGHT ACTIONS --}}
    <div class="container">

        <div class="header">
            <h4>Actions opérateur</h4>
        </div>

        <div class="body">

            <form method="POST" action="{{ url('operator/requests/'.$request->id.'/note') }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Note opérateur</label>
                    <textarea name="operator_note" class="form-control" placeholder="Note pour l'administrateur"></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Envoyer la note
                </button>
            </form>

            {{-- PAYMENT VALIDATION --}}
            @if($request->status !== 'paid' 
                && $request->status !== 'delivered')
                <form method="POST" action="{{ url('operator/requests/'.$request->id.'/pay') }}" class="form">
                    @csrf

                    <div class="form-group">
                        <label>Paiement</label>
                        <textarea name="operator_note" class="form-control" placeholder="Note pour l'administrateur"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-success"
                        onclick="return confirm('Valider le paiement de cette demande ?')">
                        Marquer comme payé
                    </button>
                </form>
            @endif

            {{-- DELIVERY INFO --}}
            <div class="form-group">
                <label>Livraison</label>

                @if($request->delivery_credentials)
                    <div class="note-box-admin">
                        <pre style="white-space: pre-wrap;">{{ $request->delivery_credentials }}</pre>
                    </div>
                @else
                    <p class="value">Pas encore livré</p>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection

