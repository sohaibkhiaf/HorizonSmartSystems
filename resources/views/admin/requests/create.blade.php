@extends('admin.layouts.app-admin')

@section('page_title', 'Nouvelle demande client')
@section('page_subtitle', 'Créer une requête pour un client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/requests/create.css') }}">
@endsection

@section('content')

<div class="container">
    
    <div class="body">

        <form method="POST" action="{{ route('admin.requests.store') }}" class="form">
            @csrf

            <input type="hidden" name="subscription_id" id="subscription_id">

            <div class="form-group">
                <label>Produit sélectionné</label>

                <select name="subscription_id" id="subscription_id" class="form-control">
                    <option value="">-- Sélectionnez un produit --</option>
                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->id }}">{{ $subscription->name }} {{ $subscription->price }} DA</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Quantité</label>
                <select name="quantity" id="quantity" class="form-control">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nom client</label>
                <input 
                    type="text" 
                    name="customer_full_name" 
                    class="form-control" 
                    placeholder="Entrez le nom du client" 
                    required>
            </div>

            <div class="form-group">
                <label>Contact client</label>
                <input 
                    type="text" 
                    name="customer_contact" 
                    class="form-control" 
                    placeholder="Numéro de téléphone" 
                    required>
            </div>

            <div class="form-group">
                <label>E-mail client</label>
                <input 
                    type="text" 
                    name="customer_email" 
                    class="form-control" 
                    placeholder="E-mail" >
            </div>

            <div class="form-group">
                <label>Adresse client</label>
                <input 
                    type="text" 
                    name="customer_address" 
                    class="form-control" 
                    placeholder="Adresse" >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-main">
                    Créer la demande
                </button>

                <a href="{{ route('admin.requests.index') }}" class="btn-secondary">
                    Annuler
                </a>
            </div>

        </form>

    </div>

</div>

@endsection

