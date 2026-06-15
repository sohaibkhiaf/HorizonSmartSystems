@extends('admin.layouts.admin')

@section('page_title', 'Portails clients')
@section('page_subtitle', 'Gestion des espaces clients, démos et messages')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/customer_portals/index.css') }}">
@endsection

@section('content')

<div class="filter-container">
    <form method="GET" action="{{ route('admin.customer_portals.index') }}" class="filter-form">

        <div class="filter-group">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Rechercher par nom ou email...">
        </div>

        <div class="filter-actions">
            <button type="submit" class="filter-btn">
                <i class="fas fa-search"></i>
                Rechercher
            </button>

            <a href="{{ route('admin.customer_portals.index') }}" class="reset-btn">
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
                <th>Client</th>
                <th>Contact</th>
                <th>Entreprise</th>
                <th>Démos</th>
                <th>Messages non lus</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>#{{ $customer->id }}</td>

                    <td>
                        <strong>{{ $customer->name }}</strong><br>
                        <small>{{ $customer->email }}</small>
                    </td>

                    <td>{{ $customer->phone ?? 'N/A' }}</td>

                    <td>{{ $customer->company ?? 'N/A' }}</td>

                    <td>
                        <span class="badge muted">
                            {{ $customer->demos_count }}
                        </span>
                    </td>

                    <td>
                        @if($customer->unread_messages_count > 0)
                            <span class="badge warning">
                                {{ $customer->unread_messages_count }}
                            </span>
                        @else
                            <span class="badge success">
                                0
                            </span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.customer_portals.show', $customer->id) }}"
                           class="action-btn edit">
                            <i class="fas fa-eye"></i>
                            Gérer
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        Aucun client trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@if($customers->hasPages())
    <div class="navigator-container">
        @if ($customers->currentPage() > 1)
            <a href="{{ $customers->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $customers->url($customers->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $customers->lastPage(); $i++)
            <a href="{{ $customers->url($i) }}" class="{{ $customers->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($customers->currentPage() < $customers->lastPage())
            <a href="{{ $customers->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $customers->url($customers->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection

