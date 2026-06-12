@extends('admin.layouts.app-admin')

@section('page_title', 'Produits')
@section('page_subtitle', 'Gestion des produits de vente')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/products/index.css') }}">
@endsection

@section('content')

<div class="header">

    <a href="{{ route('admin.store_subs.create') }}" class="header-btn">
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
            @forelse($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->name }}</td>
                    <td>
                        @if ($subscription->category == 'games')
                            Jeux
                        @elseif ($subscription->category == 'gift_cards')
                            Cartes cadeaux
                        @elseif ($subscription->category == 'software')
                            Logiciels
                        @elseif ($subscription->category == 'subscriptions')
                            Abonnements
                        @elseif ($subscription->category == 'in_game_currency')
                            Monnaie virtuelle
                        @elseif ($subscription->category == 'other')
                            Autre
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($subscription->type == 'key')
                            Clé
                        @elseif ($subscription->type == 'account')
                            Compte
                        @elseif ($subscription->type == 'invitation')
                            Invitation
                        @endif
                    </td>
                    <td>{{ $subscription->price }} DA</td>

                    <td>
                        <div class="table-actions">

                            <a href="{{ route('admin.store_subs.edit', $subscription->id) }}" 
                               class="action-btn edit">
                                <i class="fas fa-pen"></i>
                            </a>

                            <form action="{{ route('admin.store_subs.destroy', $subscription->id) }}" 
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

