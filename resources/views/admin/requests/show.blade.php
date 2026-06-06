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
            <h3>Informations client</h3>
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
                <p class="value">
                    {{ $request->product->name ?? 'N/A' }}
                </p>
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
            <h3>Actions admin</h3>
        </div>

        <div class="body">

            {{-- STATUS FORM --}}
            <form method="POST" action="{{ url('admin/requests/'.$request->id.'/availability') }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="available">Disponible</option>
                        <option value="not_available">Non disponible</option>
                        <option value="processing">En cours</option>

                        <option value="pending">En attente [SYSTEME]</option>
                        <option value="paid">Payé [OPERATEUR]</option>
                        <option value="delivered">Livré [SYSTEME]</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Note admin</label>
                    <textarea name="admin_note" class="form-control" placeholder="Note pour l'opérateur"></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Mettre à jour
                </button>

            </form>

            {{-- DELIVERY FORM --}}
            <form method="POST" action="{{ url('admin/requests/'.$request->id.'/deliver') }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Livraison</label>
                    <textarea name="credentials" class="form-control" placeholder="Login / mot de passe / lien / code">{{ $request->delivery_credentials ?? '' }}</textarea>
                </div>

                @if($request->delivery_credentials)
                    <button type="submit" class="btn-success">
                        Mettre à jour la livraison
                    </button>
                @else
                    <button type="submit" class="btn-success">
                        Marquer comme livré
                    </button>
                @endif

            </form>

            <form action="{{ route('admin.requests.destroy', $request->id) }}" 
                    method="POST">
                @csrf
                @method('DELETE')

                <div class="form-group">
                    <label>Supprimer</label>
                </div>

                <button type="submit" class="btn-delete"
                        onclick="return confirm('Supprimer cette demande ?')">
                    Supprimer la demande
                </button>
            </form>

        </div>

    </div>

</div>

@endsection

