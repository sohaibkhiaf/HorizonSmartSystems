

@extends('admin.layouts.app-operator')

@section('page_title', 'Détails de la demande')
@section('page_subtitle', 'Gestion de la requête client')

@section('content')

<div class="grid grid-2">

    {{-- LEFT INFO --}}
    <div class="card">

        <div class="card-header">
            <h3>Informations client</h3>
        </div>

        <div class="card-body">

            <div class="info-block">
                <p class="label">Nom</p>
                <p class="value">{{ $request->client_name }}</p>
            </div>

            <div class="info-block">
                <p class="label">Contact</p>
                <p class="value">{{ $request->client_contact }}</p>
            </div>

            <div class="divider"></div>

            <div class="info-block">
                <p class="label">Produit</p>
                <p class="value">{{ $request->product->name ?? 'N/A' }}</p>
            </div>

            <div class="divider"></div>

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

            <div class="divider"></div>

            <div class="info-block">
                <p class="label">Notes admin</p>
                @if($request->admin_notes)
                    <div class="note-box" style="white-space: pre-line;">{{ $request->admin_notes }}</div>
                @else
                    <p class="muted">Aucune note encore</p>
                @endif
            </div>

            <div class="info-block">
                <p class="label">Notes opérateur</p>
                @if($request->operator_notes)
                    <div class="note-box" style="white-space: pre-line;">{{ $request->operator_notes }}</div>
                @else
                    <p class="muted">Aucune note encore</p>
                @endif
            </div>

        </div>

    </div>


    {{-- RIGHT ACTIONS --}}
    <div class="card">

        <div class="card-header">
            <h3>Actions opérateur</h3>
        </div>

        <div class="card-body">

            {{-- OPERATOR RESPONSE --}}
            <form method="POST" action="{{ url('operator/requests/'.$request->id.'/note') }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Note à envoyer</label>
                    <textarea name="operator_notes" class="form-control" placeholder="Ajouter une note interne..."></textarea>
                </div>

                <button type="submit" class="btn-primary w-full">
                    Envoyer la note
                </button>
            </form>

            {{-- PAYMENT VALIDATION --}}
            @if($request->status !== 'paid' 
                && $request->status !== 'delivered')
                <div class="divider"></div>
                <div class="form-group">
                    <label>Paiement</label>
                    
                    
                    <form method="POST" action="{{ url('operator/requests/'.$request->id.'/pay') }}" class="form">
                        @csrf
                        <button type="submit" class="btn-success w-full">
                            Marquer comme payé
                        </button>
                    </form>
                </div>
            @endif
            

            <div class="divider"></div>

            {{-- DELIVERY INFO --}}
            <div class="form-group">
                <label>Livraison</label>

                @if($request->delivery_credentials)
                    <div class="note-box">
                        <pre style="white-space: pre-wrap;">{{ $request->delivery_credentials }}</pre>
                    </div>
                @else
                    <p class="muted">Pas encore livré</p>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection