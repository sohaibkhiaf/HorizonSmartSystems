


@extends('admin.layouts.app-operator')

@section('page_title', 'Nouvelle demande client')
@section('page_subtitle', 'Créer une requête pour un client')

@section('content')

{{-- CREATE REQUEST --}}
<div class="card">
    
    <div class="card-body">

        <form method="POST" action="{{ route('operator.requests') }}" class="form">
            @csrf

            <input type="hidden" name="product_id" id="product_id">

            <div class="form-group">
                <label>Produit sélectionné</label>

                <select name="product_id" id="product_id" class="form-control">
                    <option value="">-- Sélectionnez un produit --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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

            <button type="submit" class="btn-success w-full">
                Créer la demande
            </button>

        </form>

    </div>

</div>


@endsection

