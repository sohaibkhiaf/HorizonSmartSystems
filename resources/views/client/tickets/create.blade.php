@extends('client.layouts.client')

@section('title', 'Nouveau ticket')
@section('page_title', 'Nouveau ticket')
@section('page_subtitle', 'Decrivez votre besoin pour recevoir une reponse precise')

@section('style')
<style>
    .form-panel{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;max-width:860px}
    .form-grid{display:grid;gap:16px}.form-group label{display:block;font-weight:700;margin-bottom:7px}.form-group input,.form-group textarea{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:12px;font:inherit}
    .form-group textarea{min-height:180px;resize:vertical}.help{color:#6b7280;font-size:13px;margin-top:6px}.actions{display:flex;gap:10px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:10px 13px;font-size:13px;font-weight:700;border:0;cursor:pointer}
    .btn-primary{background:#2563eb;color:#fff}.btn-light{background:#f3f4f6;color:#111827}
</style>
@endsection

@section('content')
<div class="form-panel">
    <form method="POST" action="{{ route('client.tickets.store') }}" class="form-grid">
        @csrf
        <div class="form-group">
            <label>Sujet</label>
            <input name="subject" value="{{ old('subject') }}" placeholder="Ex: Probleme d'activation Windows" required>
            <p class="help">Utilisez un sujet court et clair.</p>
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" placeholder="Expliquez le contexte, le service concerne et ce que vous attendez." required>{{ old('message') }}</textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer le ticket</button>
            <a href="{{ route('client.tickets.index') }}" class="btn btn-light">Annuler</a>
        </div>
    </form>
</div>
@endsection
