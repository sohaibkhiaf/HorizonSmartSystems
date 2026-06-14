@extends('admin.layouts.admin')

@section('page_title', 'Abonnements')
@section('page_subtitle', 'Gestion des abonnements de store')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_subscriptions/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.store_subs.create') }}" class="header-btn">
        <i class="fas fa-plus"></i>
        Ajouter un abonnement
    </a>
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
                        @elseif ($subscription->type == 'invitation')
                            Invitation
                        @else
                            N/A
                        @endif
                    </td>

                    <td>{{ $subscription->price }} DA</td>

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
                    <td colspan="6" class="empty">
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
            <a href="{{$subscriptions->previousPageUrl()}}"><i class="fa-solid fa-angle-left"></i></a>
        @else
            <a href="{{$subscriptions->url($subscriptions->currentPage())}}"><i class="fa-solid fa-angle-left"></i></a>
        @endif

        @for ($i = 1; $i <= $subscriptions->lastPage(); $i++)
            @if ($subscriptions->currentPage() == $i)
                <a href="{{$subscriptions->url($i)}}" class="active">{{$i}}</a>
            @else
                <a href="{{$subscriptions->url($i)}}">{{$i}}</a>
            @endif
        @endfor

        @if ($subscriptions->currentPage() < $subscriptions->lastPage())
            <a href="{{$subscriptions->nextPageUrl()}}"><i class="fa-solid fa-angle-right"></i></a>
        @else
            <a href="{{$subscriptions->url($subscriptions->currentPage())}}"><i class="fa-solid fa-angle-right"></i></a>
        @endif
    </div>
@endif

@endsection
