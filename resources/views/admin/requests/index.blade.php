@extends('admin.layouts.admin')

@section('page_title', 'Demandes')
@section('page_subtitle', 'Gestion des requêtes clients')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/requests/index.css') }}">
@endsection

@section('content')

<div class="filter-container">
    <form method="GET" action="{{ route('admin.requests.index') }}" class="filter-form">
        <div class="filter-group">
            <select name="status" id="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>

                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                    En attente
                </option>

                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                    En cours
                </option>

                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                    Disponible
                </option>

                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>
                    Payé
                </option>

                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                    Livré
                </option>

                <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>
                    Reportée
                </option>

                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                    Annulée
                </option>

            </select>
        </div>

        @if(request('status'))
            <a href="{{ route('admin.requests.index') }}" class="reset-filter">
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
                <th>Produit</th>
                <th>Client</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Action</th>
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
                        <small style="color:#6b7280;">
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
                            </a>

                            <form action="{{ route('admin.requests.destroy', $request->id) }}" 
                                  method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" 
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cette demande ?')">
                                    <i class="fas fa-trash"></i>
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

    <div class="navigator-container">
        @if ($requests->currentPage() > 1)
            <a href="{{$requests->previousPageUrl().'&status='.$status}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @else
            <a href="{{$requests->url($requests->currentPage()).'&status='.$status}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @endif

        @for ($i = 1; $i <= $requests->lastPage(); $i++)
            @if ($requests->currentPage() == $i)
                <a href="{{$requests->url($i).'&status='.$status}}" class="active">{{$i}}</a>
            @else
                <a href="{{$requests->url($i).'&status='.$status}}">{{$i}}</a>
            @endif
        @endfor

        @if ($requests->currentPage() < $requests->lastPage())
            <a href="{{$requests->nextPageUrl().'&status='.$status}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @else
            <a href="{{$requests->url($requests->currentPage()).'&status='.$status}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @endif
    </div>

</div>

@endsection

