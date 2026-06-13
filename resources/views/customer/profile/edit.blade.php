@extends('customer.layouts.customer')

@section('title', 'Mon profil')
@section('page_title', 'Mon profil')
@section('page_subtitle', 'Informations utilisees pour le suivi commercial et le support')

@section('style')
<style>
    .profile-layout{display:grid;grid-template-columns:360px 1fr;gap:18px}
    .panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px}.avatar{width:70px;height:70px;border-radius:12px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:14px}
    .panel h2,.panel h3{margin-top:0}.muted{color:#6b7280;font-size:13px;margin:5px 0}.info{display:grid;gap:10px;margin-top:18px}.info div{background:#f9fafb;border:1px solid #eef2f7;border-radius:8px;padding:12px}.info span{display:block;color:#6b7280;font-size:12px}.info strong{display:block;margin-top:5px;word-break:break-word}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.form-group label{display:block;font-weight:700;margin-bottom:7px}.form-group input{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}.full{grid-column:1/-1}
    .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 13px;font-size:13px;font-weight:700;border:0;cursor:pointer}.btn-primary{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}
    @media(max-width:900px){.profile-layout,.form-grid{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
@php($user = auth()->user())
<div class="profile-layout">
    <aside class="panel">
        <div class="avatar"><i class="fas fa-user"></i></div>
        <h2>{{ $user->name }}</h2>
        <p class="muted">{{ $user->company ?: 'Entreprise non renseignee' }}</p>
        <div class="info">
            <div><span>Email</span><strong>{{ $user->email }}</strong></div>
            <div><span>Telephone</span><strong>{{ $user->phone ?: 'Non renseigne' }}</strong></div>
            <div><span>Type de compte</span><strong>Client</strong></div>
            <div><span>Compte cree le</span><strong>{{ $user->created_at->format('d/m/Y') }}</strong></div>
        </div>
    </aside>

    <section class="panel">
        <h3>Modifier mes informations</h3>
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Adresse email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label>Telephone</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Ex: 0550 00 00 00">
                </div>
                <div class="form-group">
                    <label>Entreprise</label>
                    <input name="company" value="{{ old('company', $user->company) }}" placeholder="Nom de votre entreprise">
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="{{ route('customer.dashboard.index') }}" class="btn btn-light">Retour au tableau de bord</a>
            </div>
        </form>
    </section>
</div>
@endsection
