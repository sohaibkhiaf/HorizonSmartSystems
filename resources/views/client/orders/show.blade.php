@extends('client.layouts.client')

@section('title', 'Detail commande')
@section('page_title', 'Detail de commande')
@section('page_subtitle', 'Informations completes sur votre achat')

@section('style')
<style>
    .order-layout{display:grid;grid-template-columns:1.2fr .8fr;gap:18px}
    .panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px}
    .hero{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:18px}
    .hero h2{margin:0;font-size:24px}.muted{color:#6b7280;font-size:13px;margin:6px 0 0}
    .badge{border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
    .badge.completed{background:#ecfdf5;color:#166534}.badge.pending,.badge.processing{background:#fffbeb;color:#92400e}.badge.cancelled{background:#fef2f2;color:#991b1b}
    .info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .info{background:#f9fafb;border:1px solid #eef2f7;border-radius:8px;padding:14px}.info span{display:block;color:#6b7280;font-size:12px}.info strong{display:block;margin-top:6px}
    .timeline{display:grid;gap:12px}.step{display:flex;gap:10px;align-items:flex-start}.dot{width:12px;height:12px;border-radius:50%;background:#2563eb;margin-top:4px}
    .btn{display:inline-flex;gap:8px;align-items:center;background:#f3f4f6;color:#111827;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700}
    @media(max-width:820px){.order-layout,.info-grid{grid-template-columns:1fr}.hero{flex-direction:column}}
</style>
@endsection

@section('content')
@php
    $labels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Terminee', 'cancelled' => 'Annulee'];
@endphp
<div class="order-layout">
    <section class="panel">
        <div class="hero">
            <div>
                <h2>{{ $order->name }}</h2>
                <p class="muted">Commande #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} creee le {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="badge {{ $order->status }}">{{ $labels[$order->status] ?? $order->status }}</span>
        </div>

        <div class="info-grid">
            <div class="info"><span>Montant</span><strong>{{ number_format($order->price, 0, ',', ' ') }} DA</strong></div>
            <div class="info"><span>Statut</span><strong>{{ $labels[$order->status] ?? $order->status }}</strong></div>
            <div class="info"><span>Client</span><strong>{{ auth()->user()->name }}</strong></div>
            <div class="info"><span>Derniere mise a jour</span><strong>{{ $order->updated_at->format('d/m/Y H:i') }}</strong></div>
        </div>
    </section>

    <aside class="panel">
        <h3 style="margin-top:0;">Suivi</h3>
        <div class="timeline">
            <div class="step"><span class="dot"></span><div><strong>Commande enregistree</strong><p class="muted">Votre achat est visible dans l'espace client.</p></div></div>
            <div class="step"><span class="dot"></span><div><strong>Traitement administratif</strong><p class="muted">Notre equipe met a jour le statut depuis le panneau admin.</p></div></div>
            <div class="step"><span class="dot"></span><div><strong>Livraison ou activation</strong><p class="muted">Les acces apparaissent dans Services ou Abonnements.</p></div></div>
        </div>
        <div style="margin-top:18px;">
            <a class="btn" href="{{ route('client.orders.index') }}"><i class="fas fa-arrow-left"></i> Retour aux commandes</a>
        </div>
    </aside>
</div>
@endsection
