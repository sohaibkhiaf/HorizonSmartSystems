@extends('admin.layouts.app-admin')

@section('page_title', 'Produits')
@section('page_subtitle', 'Gestion des produits de vente')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/products/index.css') }}">
@endsection

@section('content')

<div class="header">

    <a href="{{ route('admin.products.create') }}" class="header-btn">
        <i class="fas fa-plus"></i> Ajouter
    </a>
</div>

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>
                        @if ($product->category == 'games')
                            Jeux
                        @elseif ($product->category == 'gift_cards')
                            Cartes cadeaux
                        @elseif ($product->category == 'software')
                            Logiciels
                        @elseif ($product->category == 'subscriptions')
                            Abonnements
                        @elseif ($product->category == 'in_game_currency')
                            Monnaie virtuelle
                        @elseif ($product->category == 'other')
                            Autre
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($product->type == 'key')
                            Clé
                        @elseif ($product->type == 'account')
                            Compte
                        @elseif ($product->type == 'invitation')
                            Invitation
                        @endif
                    </td>
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
                    <td colspan="4" class="empty">
                        Aucun produit trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection

