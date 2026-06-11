@extends('client.layouts.client')

@section('title', 'Mes services')
@section('page_title', 'Mes services')
@section('page_subtitle', 'Acces, licences, liens de telechargement et etat des services')

@section('style')
<style>
    .service-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .service-card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px;display:grid;gap:14px}
    .head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.head h3{margin:0;font-size:18px}
    .muted{color:#6b7280;font-size:13px;margin:4px 0 0}.badge{border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
    .badge.active{background:#ecfdf5;color:#166534}.badge.maintenance{background:#eff6ff;color:#1d4ed8}.badge.expired,.badge.suspended{background:#fef2f2;color:#991b1b}
    .meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.meta div{background:#f9fafb;border:1px solid #eef2f7;border-radius:8px;padding:12px}
    .meta span{display:block;color:#6b7280;font-size:12px}.meta strong{display:block;margin-top:5px;word-break:break-word}
    .actions{display:flex;gap:10px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 12px;font-size:13px;font-weight:700}
    .btn-primary{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}.empty{background:#fff;border:1px dashed #cbd5e1;border-radius:8px;padding:30px;text-align:center;color:#64748b}
    @media(max-width:860px){.service-grid,.meta{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@php
    $labels = ['active' => 'Actif', 'maintenance' => 'Maintenance', 'expired' => 'Expire', 'suspended' => 'Suspendu'];
@endphp

@if($services->count())
    <div class="service-grid">
        @foreach($services as $service)
            <article class="service-card">
                <div class="head">
                    <div>
                        <h3>{{ $service->name }}</h3>
                        <p class="muted">Ajoute le {{ $service->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="badge {{ $service->status }}">{{ $labels[$service->status] ?? $service->status }}</span>
                </div>
                <div class="meta">
                    <div><span>Expiration</span><strong>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'Sans expiration' }}</strong></div>
                    <div><span>Cle ou licence</span><strong>{{ $service->license_key ?: 'Non renseignee' }}</strong></div>
                </div>
                <div class="actions">
                    @if($service->download_link)
                        <a class="btn btn-primary" href="{{ $service->download_link }}" target="_blank" rel="noopener"><i class="fas fa-download"></i> Telecharger</a>
                    @endif
                    <a class="btn btn-light" href="{{ route('client.tickets.create') }}"><i class="fas fa-headset"></i> Demander de l'aide</a>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="empty">Aucun service actif n'est encore disponible dans votre espace.</div>
@endif
@endsection
