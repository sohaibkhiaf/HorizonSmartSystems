@extends('admin.layouts.admin')

@section('page_title', 'Nouvelle demande client')
@section('page_subtitle', 'Créer une demande pour un client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/store_requests/create.css') }}?v={{ config('app.asset_version') }}">
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
                        <option value="{{ $subscription->id }}"
                                data-price="{{ $subscription->price }}"
                                data-discount="{{ $subscription->discount ?? 0 }}"
                                {{ old('subscription_id') }}>
                            {{ $subscription->name }} | {{ $subscription->price }} DA
                            {{!empty($subscription->discount) && $subscription->discount >0 ? ' | REMISE '. $subscription->discount. ' DA' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Quantité <span class="required">*</span></label>

                <input
                    type="number"
                    name="quantity"
                    id="quantity"
                    class="form-control"
                    value="{{ old('quantity', 1) }}"
                    min="1"
                    step="1"
                    placeholder="Entrez la quantité"
                    required>
            </div>

            <div class="form-group">
                <label>Prix unitaire <span class="required">*</span></label>

                <input
                    type="number"
                    name="unit_price"
                    id="unit_price"
                    class="form-control"
                    value="{{ old('unit_price') }}"
                    min="1"
                    step="1"
                    placeholder="Entrez le prix unitaire"
                    required>
            </div>

            <div class="form-group">
                <label>Total</label>

                <input
                    type="number"
                    name="total"
                    id="total" 
                    class="form-control"
                    value="{{ old('total') }}"
                    placeholder="Total calculé"
                    readonly>
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

<script>
    const subscriptionSelect = document.querySelector('select[name="subscription_id"]');
    const unitPriceInput     = document.getElementById('unit_price');
    const quantityInput      = document.getElementById('quantity');
    const totalInput         = document.getElementById('total');

    function recalcTotal() {
        const price = parseFloat(unitPriceInput.value) || 0;
        const qty   = parseInt(quantityInput.value)    || 1;
        totalInput.value = price ? price * qty : '';
    }

    function onSubscriptionChange() {
        const selected = subscriptionSelect.options[subscriptionSelect.selectedIndex];
        const price    = parseFloat(selected.dataset.price    || 0);
        const discount = parseFloat(selected.dataset.discount || 0);

        unitPriceInput.value = (discount > 0) ? discount : (price || '');
        recalcTotal();
    }

    subscriptionSelect.addEventListener('change', onSubscriptionChange);
    unitPriceInput.addEventListener('input', recalcTotal);
    quantityInput.addEventListener('input', recalcTotal);
</script>

@endsection
