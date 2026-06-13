@extends('customer.layouts.customer')

@section('title', 'Tableau de bord client')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble de vos commandes, services, abonnements et tickets')

@section('style')
<style>
    .client-grid{display:grid;gap:18px}
    .stats-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
    .stat-card,.panel,.quick-card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}
    .stat-card{display:flex;gap:14px;align-items:center}
    .stat-icon{width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#eef2ff;color:#3730a3}
    .stat-card h3{margin:0;font-size:24px}
    .stat-card p,.muted{margin:4px 0 0;color:#6b7280;font-size:13px}
    .section-grid{display:grid;grid-template-columns:1.4fr .8fr;gap:18px}
    .panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
    .panel-head h2{margin:0;font-size:18px}
    .btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700}
    .btn-primary{background:#2563eb;color:#fff}
    .btn-light{background:#f3f4f6;color:#111827}
    .table{width:100%;border-collapse:collapse}
    .table th{text-align:left;color:#6b7280;font-size:12px;text-transform:uppercase;border-bottom:1px solid #e5e7eb;padding:10px}
    .table td{padding:12px 10px;border-bottom:1px solid #f1f5f9;font-size:14px}
    .badge{display:inline-flex;align-items:center;border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}
    .badge.active,.badge.completed{background:#ecfdf5;color:#166534}
    .badge.pending,.badge.processing,.badge.waiting{background:#fffbeb;color:#92400e}
    .badge.open{background:#eff6ff;color:#1d4ed8}
    .badge.expired,.badge.cancelled,.badge.cancelled{background:#fef2f2;color:#991b1b}
    .quick-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .quick-card{display:flex;align-items:center;gap:12px;color:#111827}
    .quick-card i{color:#2563eb}
    .notice{border-left:4px solid #f59e0b;background:#fffbeb;padding:12px;border-radius:8px;margin-bottom:10px}
    .empty{padding:24px;text-align:center;color:#6b7280;background:#f9fafb;border-radius:8px}
    @media(max-width:1000px){.stats-grid,.section-grid,.quick-grid{grid-template-columns:1fr 1fr}}
    @media(max-width:640px){.stats-grid,.section-grid,.quick-grid{grid-template-columns:1fr}.table{display:block;overflow-x:auto}.panel-head{align-items:flex-start;flex-direction:column}}
</style>
@endsection

@section('content')
<div class="client-grid">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-receipt"></i></div>
            <div><h3>{{ $orders_count }}</h3><p>Commandes au total</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-wallet"></i></div>
            <div><h3>{{ number_format($orders_total, 0, ',', ' ') }} DA</h3><p>Montant commande</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-cubes"></i></div>
            <div><h3>{{ $services_count }}</h3><p>Services actifs</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-headset"></i></div>
            <div><h3>{{ $open_tickets_count }}</h3><p>Tickets ouverts</p></div>
        </div>
    </div>

    <div class="quick-grid">
        <a class="quick-card" href="{{ route('customer.orders.index') }}"><i class="fas fa-list"></i><strong>Voir mes commandes</strong></a>
        <a class="quick-card" href="{{ route('customer.services.index') }}"><i class="fas fa-download"></i><strong>Acceder a mes services</strong></a>
        <a class="quick-card" href="{{ route('customer.tickets.create') }}"><i class="fas fa-plus"></i><strong>Ouvrir un ticket</strong></a>
    </div>

    <div class="section-grid">
        <section class="panel">
            <div class="panel-head">
                <h2>Commandes recentes</h2>
                <a class="btn btn-light" href="{{ route('customer.orders.index') }}">Tout afficher</a>
            </div>
            @if($recent_orders->count())
                <table class="table">
                    <thead><tr><th>Commande</th><th>Prix</th><th>Statut</th><th>Date</th></tr></thead>
                    <tbody>
                    @foreach($recent_orders as $order)
                        <tr>
                            <td><a href="{{ route('customer.orders.show', $order->id) }}"><strong>{{ $order->name }}</strong></a></td>
                            <td>{{ number_format($order->price, 0, ',', ' ') }} DA</td>
                            <td><span class="badge {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">Aucune commande disponible pour le moment.</div>
            @endif
        </section>

        <aside class="panel">
            <div class="panel-head"><h2>Notifications</h2></div>
            @forelse($expiring_subscriptions as $sub)
                <div class="notice">
                    <strong>{{ $sub->name }}</strong>
                    <p class="muted">Renouvellement prevu le {{ $sub->renewal_date->format('d/m/Y') }}.</p>
                </div>
            @empty
                <div class="empty">Aucune alerte importante.</div>
            @endforelse
        </aside>
    </div>

    <section class="panel">
        <div class="panel-head">
            <h2>Services disponibles</h2>
            <a class="btn btn-primary" href="{{ route('customer.services.index') }}"><i class="fas fa-cubes"></i> Mes services</a>
        </div>
        <div class="quick-grid">
            @forelse($recent_services as $service)
                <div class="quick-card">
                    <i class="fas fa-circle-check"></i>
                    <div>
                        <strong>{{ $service->name }}</strong>
                        <p class="muted">{{ $service->expires_at ? 'Expire le '.$service->expires_at->format('d/m/Y') : 'Sans date d\'expiration' }}</p>
                    </div>
                </div>
            @empty
                <div class="empty">Vos services apparaitront ici apres activation.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
