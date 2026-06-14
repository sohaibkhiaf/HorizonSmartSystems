@extends('admin.layouts.admin')

@section('page_title', 'Détails de la demande')
@section('page_subtitle', 'Gestion complète de la demande')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_requests/show.css') }}">
@endsection

@section('content')

<div class="grid">

    <div class="container">
        <div class="header">
            <h4>Informations client</h4>
        </div>

        <div class="body">

            <form method="POST" action="{{ route('admin.requests.updateInfo', $request->id) }}" class="form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nom complet <span class="required">*</span></label>
                    <input type="text"
                        name="customer_full_name"
                        value="{{ old('customer_full_name', $request->customer_full_name) }}"
                        placeholder="Entrez le nom complet"
                        required>
                </div>

                <div class="form-group">
                    <label>Contact <span class="required">*</span></label>
                    <input type="text"
                        name="customer_contact"
                        value="{{ old('customer_contact', $request->customer_contact) }}"
                        placeholder="Entrez le numéro de contact"
                        required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email"
                        name="customer_email"
                        value="{{ old('customer_email', $request->customer_email) }}"
                        placeholder="Entrez l'adresse e-mail">
                </div>

                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text"
                        name="customer_address"
                        value="{{ old('customer_address', $request->customer_address) }}"
                        placeholder="Entrez l'adresse">
                </div>

                <div class="form-group">
                    <label>Abonnement <span class="required">*</span></label>
                    <select name="store_subscription_id" id="subscriptionSelect" required>
                        <option value="">-- Sélectionner --</option>

                        @foreach($subscriptions as $subscription)
                            <option value="{{ $subscription->id }}"
                                    data-type="{{ $subscription->type }}"
                                    data-price="{{ $subscription->price }}"
                                    {{ old('store_subscription_id', $request->store_subscription_id) == $subscription->id ? 'selected' : '' }}>
                                {{ $subscription->name }} - {{ $subscription->price }} DA
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <input type="text" id="subscriptionType" value="" placeholder="Type d'abonnement" readonly>
                </div>

                <div class="form-group">
                    <label>Prix unitaire</label>
                    <input type="text" id="subscriptionPrice" value="" placeholder="Prix unitaire" readonly>
                </div>

                <div class="form-group">
                    <label>Quantité <span class="required">*</span></label>
                    <input type="number"
                        name="quantity"
                        id="quantity"
                        value="{{ old('quantity', $request->quantity) }}"
                        placeholder="Entrez la quantité"
                        min="1"
                        required>
                </div>

                <div class="form-group">
                    <label>Total</label>
                    <input type="text" id="totalPrice" value="" placeholder="Total calculé" readonly>
                </div>

                <button type="submit" class="btn-primary">
                    Mettre à jour les informations
                </button>
            </form>

        </div>
    </div>

    <div class="container">
        <div class="header">
            <h4>Actions admin</h4>
        </div>

        <div class="body">

            <form method="POST" action="{{ route('admin.requests.update', $request->id) }}" class="form">
                @csrf

                <div class="form-group">
                    <label>Statut <span class="required">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="processing" {{ $request->status == 'processing' ? 'selected' : '' }}>En cours</option>
                        <option value="available" {{ $request->status == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="postponed" {{ $request->status == 'postponed' ? 'selected' : '' }}>Reportée</option>
                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        <option value="paid" {{ $request->status == 'paid' ? 'selected' : '' }}>Payée</option>
                        <option value="delivered" {{ $request->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Note admin</label>
                    <textarea name="admin_note" class="form-control" placeholder="Note pour l'opérateur">{{ old('admin_note', $request->admin_note) }}</textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Mettre à jour le statut
                </button>
            </form>

        </div>
    </div>

</div>

<script>
    const subscriptionSelect = document.getElementById('subscriptionSelect');
    const subscriptionType = document.getElementById('subscriptionType');
    const subscriptionPrice = document.getElementById('subscriptionPrice');
    const quantity = document.getElementById('quantity');
    const totalPrice = document.getElementById('totalPrice');

    const types = {
        key: 'Clé',
        account: 'Compte',
        invitation: 'Invitation'
    };

    function updateSubscriptionInfo() {
        const selected = subscriptionSelect.options[subscriptionSelect.selectedIndex];
        const type = selected.dataset.type || '';
        const price = Number(selected.dataset.price || 0);
        const qty = Number(quantity.value || 1);

        subscriptionType.value = types[type] || 'N/A';
        subscriptionPrice.value = price ? `${price} DA` : 'N/A';
        totalPrice.value = price ? `${price * qty} DA` : 'N/A';
    }

    subscriptionSelect.addEventListener('change', updateSubscriptionInfo);
    quantity.addEventListener('input', updateSubscriptionInfo);

    updateSubscriptionInfo();
</script>

@endsection