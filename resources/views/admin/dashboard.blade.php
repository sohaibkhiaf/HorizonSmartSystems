@extends('admin.layouts.admin')

@section('title', 'Tableau de bord Admin')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble du site, de la boutique et de l\'espace client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endsection

@section('content')

{{-- SITE PUBLIC --}}
<h2 class="dashboard-section-title">Site public</h2>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <h3>{{ $publicStats['messages_waiting'] }}</h3>
            <p>Messages en attente</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-check"></i>
        </div>
        <div>
            <h3>{{ $publicStats['messages_seen'] }}</h3>
            <p>Messages consultés</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <div>
            <h3>{{ $publicStats['achievements_published'] }} / {{ $publicStats['achievements_total'] }}</h3>
            <p>Réalisations publiées</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <h3>{{ $publicStats['services_published'] }} / {{ $publicStats['services_total'] }}</h3>
            <p>Services publiés</p>
        </div>
    </div>

</div>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-laptop-code"></i>
        </div>
        <div>
            <h3>{{ $publicStats['software_published'] }} / {{ $publicStats['software_total'] }}</h3>
            <p>Logiciels publiés</p>
        </div>
    </div>

</div>

{{-- BOUTIQUE --}}
<h2 class="dashboard-section-title">Boutique</h2>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <h3>{{ $storeStats['products_total'] }}</h3>
            <p>Produits boutique</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_pending'] }}</h3>
            <p>Demandes en attente</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-spinner"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_processing'] }}</h3>
            <p>Demandes en cours</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_available'] }}</h3>
            <p>Demandes disponibles</p>
        </div>
    </div>

</div>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-money-bill"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_paid'] }}</h3>
            <p>Demandes payées</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-truck"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_delivered'] }}</h3>
            <p>Demandes livrées</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-calendar"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_postponed'] }}</h3>
            <p>Demandes reportées</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-xmark"></i>
        </div>
        <div>
            <h3>{{ $storeStats['requests_cancelled'] }}</h3>
            <p>Demandes annulées</p>
        </div>
    </div>

</div>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-sack-dollar"></i>
        </div>
        <div>
            <h3>{{ number_format($storeStats['revenue_paid'], 0, ',', ' ') }} DA</h3>
            <p>Chiffre boutique payé/livré</p>
        </div>
    </div>

</div>

{{-- QUICK ACTIONS --}}
<h2 class="dashboard-section-title">Actions rapides</h2>

<div class="admin-tabs">

    <a href="{{ route('admin.messages.waiting') }}" class="tab-card">
        <i class="fas fa-envelope"></i>
        <h3>Messages en attente</h3>
        <p>Consulter les nouveaux messages reçus depuis le site.</p>
    </a>

    <a href="{{ route('admin.store_subs.index') }}" class="tab-card">
        <i class="fas fa-box"></i>
        <h3>Produits boutique</h3>
        <p>Gérer les produits, clés, comptes et invitations.</p>
    </a>

    <a href="{{ route('admin.requests.index') }}" class="tab-card">
        <i class="fas fa-cart-shopping"></i>
        <h3>Demandes boutique</h3>
        <p>Suivre les commandes reçues depuis la boutique.</p>
    </a>

</div>
{{-- RECENT ACTIVITY --}}
<h2 class="dashboard-section-title">Activité récente</h2>

<div class="dashboard-sections">

    {{-- Derniers messages --}}
    <div class="content-card">
        <div class="card-header">
            <h3>Derniers messages</h3>
        </div>

        @forelse($latestMessages as $message)
            <div class="activity-item">
                <div>
                    <strong>{{ $message->full_name }}</strong>
                    <p>{{ $message->subject ?? 'Sans sujet' }}</p>
                </div>

                <span class="status-badge {{ $message->is_seen ? 'success' : 'warning' }}">
                    {{ $message->is_seen ? 'Consulté' : 'En attente' }}
                </span>
            </div>
        @empty
            <p class="empty-text">Aucun message.</p>
        @endforelse
    </div>

    {{-- Dernières demandes boutique --}}
    <div class="content-card">
        <div class="card-header">
            <h3>Dernières demandes boutique</h3>
        </div>

        @forelse($latestStoreRequests as $request)
            <div class="activity-item">
                <div>
                    <strong>{{ $request->customer_full_name }}</strong>
                    <p>
                        {{ $request->storeSubscription?->name ?? 'Produit supprimé' }}
                        × {{ $request->quantity }}
                    </p>
                </div>

                <span class="status-badge status-{{ $request->status }}">
                    @if($request->status === 'pending')
                        En attente
                    @elseif($request->status === 'processing')
                        En cours
                    @elseif($request->status === 'available')
                        Disponible
                    @elseif($request->status === 'cancelled')
                        Annulée
                    @elseif($request->status === 'postponed')
                        Reportée
                    @elseif($request->status === 'paid')
                        Payée
                    @elseif($request->status === 'delivered')
                        Livrée
                    @else
                        {{ $request->status }}
                    @endif
                </span>
            </div>
        @empty
            <p class="empty-text">Aucune demande boutique.</p>
        @endforelse
    </div>

</div>

@endsection
