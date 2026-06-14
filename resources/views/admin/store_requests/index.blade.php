@extends('admin.layouts.admin')

@section('page_title', 'Demandes')
@section('page_subtitle', 'Gestion des demandes d’abonnements')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_requests/index.css') }}">
@endsection

@section('content')

<div class="filter-container">
    <form method="GET" action="{{ route('admin.requests.index') }}" class="filter-form">

        <div class="filter-group">
            <input type="text"
                   name="search"
                   class="filter-input"
                   value="{{ request('search') }}"
                   placeholder="Rechercher par nom du client">
        </div>

        <div class="filter-group">
            <input type="date"
                name="date_from"
                class="filter-input"
                value="{{ request('date_from') }}"
                title="Date début">
        </div>

        <div class="filter-group">
            <input type="date"
                name="date_to"
                class="filter-input"
                value="{{ request('date_to') }}"
                title="Date fin">
        </div>

        <div class="filter-group">
            <select name="status" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payé</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>Reportée</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>

        <button type="submit" class="filter-btn">
            <i class="fas fa-filter"></i>
            Filtrer
        </button>

        @if(request('status') || request('search') || request('date_from') || request('date_to'))
            <a href="{{ route('admin.requests.index') }}" class="reset-filter">
                <i class="fa-solid fa-arrow-rotate-right"></i>
                Réinitialiser
            </a>
        @endif

    </form>
</div>

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Abonnement</th>
                <th>Client</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>#{{ $request->id }}</td>

                    <td>
                        {{ $request->storeSubscription->name ?? 'N/A' }}
                    </td>

                    <td>
                        <strong>{{ $request->customer_full_name }}</strong><br>
                        <small class="customer-contact">
                            {{ $request->customer_contact }}
                        </small>
                    </td>

                    <td>
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
                        @elseif($request->status === 'postponed')
                            <span class="badge muted">Reportée</span>
                        @elseif($request->status === 'cancelled')
                            <span class="badge warning">Annulée</span>
                        @endif
                    </td>

                    <td>
                        {{ $request->created_at->format('Y-m-d H:i') }}
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.requests.show', $request->id) }}"
                               class="action-btn edit">
                                <i class="fas fa-eye"></i>
                                Voir
                            </a>

                            <form action="{{ route('admin.requests.destroy', $request->id) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cette demande ?')">
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
                        Aucune demande trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>


@if($requests->hasPages())
    <div class="navigator-container">
        @if ($requests->currentPage() > 1)
            <a href="{{ $requests->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $requests->url($requests->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $requests->lastPage(); $i++)
            <a href="{{ $requests->url($i) }}" class="{{ $requests->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($requests->currentPage() < $requests->lastPage())
            <a href="{{ $requests->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $requests->url($requests->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection