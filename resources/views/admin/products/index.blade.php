@extends('admin.layouts.app-admin')

@section('page_title', 'Produits')
@section('page_subtitle', 'Gestion des produits de vente')

@section('content')

<div class="page-header">

    <a href="{{ route('admin.products.create') }}" class="btn-main">
        <i class="fas fa-plus"></i> Ajouter
    </a>
</div>

<div class="content-card table-card">

    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }} DA</td>

                    <td>
                        <div class="table-actions">

                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="action-btn edit">
                                <i class="fas fa-pen"></i>
                            </a>

                            <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" 
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer ce produit ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-users">
                        Aucun produit trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection

