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

            <form method="POST" action="{{ route('admin.store_requests.updateInfo', $request->id) }}" class="form">
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
                                    data-discount="{{ $subscription->discount ?? 0 }}"
                                    {{ old('store_subscription_id', $request->store_subscription_id) == $subscription->id ? 'selected' : '' }}>
                                {{ $subscription->name }} | {{ $subscription->price }} DA 
                                {{!empty($subscription->discount) && $subscription->discount >0 ? ' | REMISE '. $subscription->discount. ' DA' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    @php
                        $types = [
                            'key'        => 'Clé',
                            'account'    => 'Compte',
                            'invitation' => 'Invitation',
                        ];
                    @endphp
                    <input type="text"
                        id="subscriptionType"
                        value="{{ $request->storeSubscription != null ? $types[$request->storeSubscription->type] : 'N/A' }}"
                        placeholder="Type d'abonnement"
                        readonly>
                </div>

                <div class="form-group">
                    <label>Prix unitaire</label>
                    <input type="text" 
                        name="unit_price" 
                        id="subscriptionPrice" 
                        value="{{ old('unit_price', $request->unit_price) }}" 
                        placeholder="Prix unitaire">
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
                    <input type="text" 
                        id="totalPrice" 
                        value="{{ old('quantity', $request->quantity * $request->unit_price) }} DA" 
                        placeholder="Total calculé" 
                        readonly>
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

            <form method="POST" action="{{ route('admin.store_requests.update', $request->id) }}" class="form">
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
    const subscriptionType   = document.getElementById('subscriptionType');
    const subscriptionPrice  = document.getElementById('subscriptionPrice');
    const quantity           = document.getElementById('quantity');
    const totalPrice         = document.getElementById('totalPrice');

    const types = {
        key: 'Clé',
        account: 'Compte',
        invitation: 'Invitation'
    };

    function recalcTotal() {
        const price = parseFloat(subscriptionPrice.value.replace(/,/g, '')) || 0;
        const qty   = parseInt(quantity.value) || 1;
        totalPrice.value = price ? (price * qty) + ' DA' : 'N/A';
    }

    function updateSubscriptionInfo() {
        const selected = subscriptionSelect.options[subscriptionSelect.selectedIndex];
        const type     = selected.dataset.type     || '';
        const price    = parseFloat(selected.dataset.price    || 0);
        const discount = parseFloat(selected.dataset.discount || 0);

        const unitPrice = (discount > 0) ? discount : price;

        subscriptionType.value  = types[type] || 'N/A';
        subscriptionPrice.value = unitPrice ? unitPrice : '';

        recalcTotal();
    }

    subscriptionSelect.addEventListener('change', updateSubscriptionInfo);
    quantity.addEventListener('input', recalcTotal);
    subscriptionPrice.addEventListener('input', recalcTotal);

</script>

@endsection