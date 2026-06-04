@extends('admin.layouts.app-operator')

@section('page_title', 'Produits')
@section('page_subtitle', 'Sélection des produits disponibles')

@section('content')

{{-- PRODUCTS TABLE --}}
<div class="card" style="margin-bottom: 20px;">

    <div class="card-header">
        <h4>Liste des produits</h4>
    </div>

    <div class="card-body">

        <table class="table modern-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Description</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td class="font-medium">
                            {{ $product->name }}
                        </td>

                        <td class="text-muted">
                            {{ $product->description }}
                        </td>

                        <td class="text-right">
                            <button class="action-btn edit"
                                    onclick="selectProduct({{ $product->id }}, '{{ $product->name }}')">
                                <i class="fa-solid fa-check"></i>
                                Choisir
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>


{{-- CREATE REQUEST --}}
<div class="card">

    <div class="card-header">
        <h4>Créer une demande client</h4>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('operator.requests') }}" class="form">
            @csrf

            <input type="hidden" name="product_id" id="product_id">

            <div class="form-group">
                <label>Produit sélectionné</label>
                <input 
                    type="text" 
                    id="product_name" 
                    class="form-control" 
                    disabled 
                    placeholder="Aucun produit sélectionné">
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


<script>
function selectProduct(id, name) {
    document.getElementById('product_id').value = id;
    document.getElementById('product_name').value = name;
}
</script>

@endsection

