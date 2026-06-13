
@extends('admin.layouts.admin')

@section('page_title', 'Portails clients')
@section('page_subtitle', 'Gestion des espaces clients, démos et messages')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/customer_portals/index.css') }}">
@endsection

@section('content')

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
    <div class="pagination-wrapper">
        {{ $customers->links() }}
    </div>
@endif

@endsection

