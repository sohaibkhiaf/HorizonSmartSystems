@extends('admin.layouts.admin')

@section('page_title', 'Produits')
@section('page_subtitle', 'Gestion des produits de store')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_subscriptions/index.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.store_subs.create') }}" class="header-btn">
        <i class="fas fa-plus"></i>
        Ajouter un produit
    </a>
</div>

<div class="filter-container">
    <form method="GET" action="{{ route('admin.store_subs.index') }}" class="filter-form">

        <div class="filter-group">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Rechercher par nom...">
        </div>

        <div class="filter-group">
            <select name="category">
                <option value="">Toutes les catégories</option>
                <option value="games" {{ request('category') == 'games' ? 'selected' : '' }}>
                    Jeux
                </option>
                <option value="gift_cards" {{ request('category') == 'gift_cards' ? 'selected' : '' }}>
                    Cartes cadeaux
                </option>
                <option value="software" {{ request('category') == 'software' ? 'selected' : '' }}>
                    Logiciels
                </option>
                <option value="subscriptions" {{ request('category') == 'subscriptions' ? 'selected' : '' }}>
                    Abonnements
                </option>
                <option value="in_game_currency" {{ request('category') == 'in_game_currency' ? 'selected' : '' }}>
                    Monnaie virtuelle
                </option>
                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>
                    Autre
                </option>
            </select>
        </div>

        <div class="filter-group">
            <select name="type">
                <option value="">Tous les types</option>
                <option value="key" {{ request('type') == 'key' ? 'selected' : '' }}>
                    Clé
                </option>
                <option value="account" {{ request('type') == 'account' ? 'selected' : '' }}>
                    Compte
                </option>
                <option value="activation_link" {{ request('type') == 'activation_link' ? 'selected' : '' }}>
                    Lien d'activation
                </option>
            </select>
        </div>

        <div class="filter-group">
            <select name="is_published">
                <option value="">Toutes les visibilités</option>
                <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>
                    Publié
                </option>
                <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>
                    Masqué
                </option>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i>
                Filtrer
            </button>

            <a href="{{ route('admin.store_subs.index') }}" class="reset-btn">
                <i class="fas fa-rotate-left"></i>
                Réinitialiser
            </a>
        </div>

    </form>
</div>

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Remise</th>
                <th>Priorité</th>
                <th>Visibilité</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($subscriptions as $subscription)
                <tr>
                    <td>#{{ $subscription->id }}</td>

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
                        @elseif ($subscription->type == 'activation_link')
                            Lien d'activation
                        @else
                            N/A
                        @endif
                    </td>

                    <td>{{ $subscription->price }} DA</td>

                    <td>{{ !empty($subscription->discount) ? $subscription->discount.' DA' : '—' }}</td>

                    <td>{{ $subscription->priority ?? 0 }}</td>

                    <td>
                        @if ($subscription->is_published)
                            <span class="status-badge published">Publié</span>
                        @else
                            <span class="status-badge hidden">Masqué</span>
                        @endif
                    </td>

                    <td>
                        <div class="table-actions">

                            <a href="{{ route('admin.store_subs.edit', $subscription->id) }}"
                               class="action-btn edit">
                                <i class="fas fa-pen"></i>
                                Modifier
                            </a>

                            <form action="{{ route('admin.store_subs.destroy', $subscription->id) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cet abonnement ?')">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty">
                        Aucun abonnement trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@if ($subscriptions->hasPages())
    <div class="navigator-container">
        @if ($subscriptions->currentPage() > 1)
            <a href="{{ $subscriptions->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $subscriptions->url($subscriptions->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $subscriptions->lastPage(); $i++)
            @if ($subscriptions->currentPage() == $i)
                <a href="{{ $subscriptions->url($i) }}" class="active">{{ $i }}</a>
            @else
                <a href="{{ $subscriptions->url($i) }}">{{ $i }}</a>
            @endif
        @endfor

        @if ($subscriptions->currentPage() < $subscriptions->lastPage())
            <a href="{{ $subscriptions->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $subscriptions->url($subscriptions->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection
