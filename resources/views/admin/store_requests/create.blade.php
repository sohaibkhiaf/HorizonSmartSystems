@extends('admin.layouts.admin')

@section('page_title', 'Nouvelle demande client')
@section('page_subtitle', 'Créer une demande pour un client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_requests/create.css') }}">
@endsection

@section('content')

<div class="container">
    <div class="body">

        <form method="POST" action="{{ route('admin.store_requests.store') }}" class="form">
            @csrf

            <div class="form-group">
                <label>Abonnement sélectionné <span class="required">*</span></label>

                <select name="subscription_id" class="form-control" required>
                    <option value="">-- Sélectionnez un abonnement --</option>

                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->id }}">
                            {{ $subscription->name }} - {{ $subscription->price }} DA
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Quantité <span class="required">*</span></label>

                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    value="{{ old('quantity', 1) }}"
                    min="1"
                    step="1"
                    placeholder="Entrez la quantité"
                    required>
            </div>

            <div class="form-group">
                <label>Nom client <span class="required">*</span></label>

                <input
                    type="text"
                    name="customer_full_name"
                    class="form-control"
                    value="{{ old('customer_full_name') }}"
                    placeholder="Entrez le nom du client"
                    required>
            </div>

            <div class="form-group">
                <label>Contact client <span class="required">*</span></label>

                <input
                    type="text"
                    name="customer_contact"
                    class="form-control"
                    value="{{ old('customer_contact') }}"
                    placeholder="Numéro de téléphone"
                    required>
            </div>

            <div class="form-group">
                <label>E-mail client</label>

                <input
                    type="email"
                    name="customer_email"
                    class="form-control"
                    value="{{ old('customer_email') }}"
                    placeholder="E-mail">
            </div>

            <div class="form-group">
                <label>Adresse client</label>

                <input
                    type="text"
                    name="customer_address"
                    class="form-control"
                    value="{{ old('customer_address') }}"
                    placeholder="Adresse">
            </div>

            <div class="form-actions full-width">
                <button type="submit" class="btn-main">
                    Créer la demande
                </button>

                <a href="{{ route('admin.store_requests.index') }}" class="btn-secondary">
                    Annuler
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
