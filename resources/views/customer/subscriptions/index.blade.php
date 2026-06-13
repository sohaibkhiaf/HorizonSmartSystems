@extends('customer.layouts.customer')

@section('title', 'Mes abonnements')
@section('page_title', 'Mes abonnements')
@section('page_subtitle', 'Suivi des renouvellements, prix et statuts')

@section('style')
<style>
    .summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:18px}.card,.panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px}
    .card span{color:#6b7280;font-size:13px}.card strong{display:block;font-size:24px;margin-top:6px}
    .subscription-list{display:grid;gap:14px}.subscription{display:grid;grid-template-columns:1fr auto auto;gap:14px;align-items:center;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px}
    .subscription h3{margin:0;font-size:17px}.muted{color:#6b7280;font-size:13px;margin:5px 0 0}
    .badge{border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}.badge.active{background:#ecfdf5;color:#166534}.badge.pending{background:#fffbeb;color:#92400e}.badge.cancelled,.badge.expired{background:#fef2f2;color:#991b1b}
    .renewal{background:#f9fafb;border-radius:8px;padding:10px 12px;text-align:right}.renewal span{display:block;color:#6b7280;font-size:12px}.renewal strong{display:block;margin-top:3px}
    .empty{background:#fff;border:1px dashed #cbd5e1;border-radius:8px;padding:30px;text-align:center;color:#64748b}
    .btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700}.btn-light{background:#f3f4f6;color:#111827}
    @media(max-width:760px){.summary{grid-template-columns:1fr}.subscription{grid-template-columns:1fr;align-items:flex-start}.renewal{text-align:left}}
</style>
@endsection

@section('content')
@php
    $labels = ['active' => 'Actif', 'pending' => 'En attente', 'cancelled' => 'Annule', 'expired' => 'Expire'];
@endphp

<div class="summary">
    <div class="card"><span>Abonnements actifs</span><strong>{{ $subscriptions->where('status', 'active')->count() }}</strong></div>
    <div class="card"><span>Total mensuel estime</span><strong>{{ number_format($subscriptions->where('status', 'active')->sum('price'), 0, ',', ' ') }} DA</strong></div>
    <div class="card"><span>Prochain renouvellement</span><strong>{{ optional($subscriptions->where('status', 'active')->sortBy('renewal_date')->first()?->renewal_date)->format('d/m/Y') ?? '-' }}</strong></div>
</div>

@if($subscriptions->count())
    <div class="subscription-list">
        @foreach($subscriptions as $sub)
            <article class="subscription">
                <div>
                    <h3>{{ $sub->name }}</h3>
                    <p class="muted">Prix: {{ number_format($sub->price, 0, ',', ' ') }} DA - Cree le {{ $sub->created_at->format('d/m/Y') }}</p>
                </div>
                <span class="badge {{ $sub->status }}">{{ $labels[$sub->status] ?? $sub->status }}</span>
                <div class="renewal"><span>Renouvellement</span><strong>{{ $sub->renewal_date->format('d/m/Y') }}</strong></div>
                <div>
                    <a class="btn btn-light" href="{{ route('customer.tickets.create') }}"><i class="fas fa-headset"></i> Demander de l'aide</a>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="empty">Aucun abonnement n'est encore rattache a votre compte.</div>
@endif
@endsection
