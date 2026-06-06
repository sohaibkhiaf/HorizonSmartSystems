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

            <input type="hidden" name="product_id" id="product_id">

            <div class="form-group">
                <label>Produit sélectionné</label>

                <select name="product_id" id="product_id" class="form-control">
                    <option value="">-- Sélectionnez un produit --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} {{ $product->price }} DA</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Nom client</label>
                <input 
                    type="text" 
                    name="client_name" 
                    class="form-control" 
                    placeholder="Entrez le nom du client" 
                    required>
            </div>

            <div class="form-group">
                <label>Contact client</label>
                <input 
                    type="text" 
                    name="client_contact" 
                    class="form-control" 
                    placeholder="Numéro de téléphone ou email" 
                    required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-success">
                    <i class="fas fa-save"></i>
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

