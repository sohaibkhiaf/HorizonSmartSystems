@extends('layouts.store')

@section('title', __('order.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/order.css') }}">
@endsection

@section('content')

@php
    $categories = [
        'games' => __('store.categories.games'),
        'gift_cards' => __('store.categories.gift_cards'),
        'software' => __('store.categories.software_plural'),
        'subscriptions' => __('store.categories.subscriptions'),
        'in_game_currency' => __('store.categories.in_game_currency'),
        'other' => __('store.categories.other'),
    ];

    $types = [
        'key' => __('store.types.key'),
        'account' => __('store.types.account'),
        'invitation' => __('store.types.invitation'),
    ];
@endphp

<main class="order-page {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    <a href="{{ route('store.index') }}" class="back-link">
        <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-arrow-right' : 'fa-solid fa-arrow-left' }}"></i>
        {{ __('order.back_to_catalog') }}
    </a>

    <form
        id="orderForm"
        action="{{ route('store.confirm', ['subscription' => $subscription]) }}"
        method="POST"
        class="order-container">
        @csrf

        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
        <input type="hidden" name="quantity" id="hidden_quantity" value="1">

        {{-- Order Form --}}
        <section class="order-card">
            <div class="section-head">
                <div class="section-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>

                <div>
                    <h1>{{ __('order.form_title') }}</h1>
                    <p>{{ __('order.form_description') }}</p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="full_name">
                        {{ __('order.full_name') }}
                        <span>*</span>
                    </label>

                    <input
                        id="full_name"
                        type="text"
                        name="full_name"
                        value="{{ old('full_name') }}"
                        placeholder="{{ __('order.full_name_placeholder') }}"
                        required>

                    @error('full_name')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">
                        {{ __('order.phone') }}
                        <span>*</span>
                    </label>

                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="{{ __('order.phone_placeholder') }}"
                        required>

                    @error('phone')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">
                        {{ __('order.email') }}
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="{{ __('order.email_placeholder') }}">

                    @error('email')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">
                        {{ __('order.address') }}
                    </label>

                    <input
                        id="address"
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="{{ __('order.address_placeholder') }}">

                    @error('address')
                        <small>{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <p class="secure-message">
                <i class="fa-solid fa-shield-halved"></i>
                {{ __('order.secure_message') }}
            </p>
        </section>

        {{-- Order Summary --}}
        <aside class="summary-card">
            <h2>{{ __('order.summary_title') }}</h2>

            <div class="subscription-summary">
                <div class="summary-image">
                    @if($subscription->image)
                        <img
                            src="{{ asset('uploads/' . $subscription->image) }}"
                            alt="{{ $subscription->name }}"
                            loading="lazy">
                    @else
                        <div class="image-placeholder">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                    @endif
                </div>

                <div class="subscription-info">
                    <h3>{{ $subscription->name }}</h3>

                    @if(!empty($subscription->description))
                        <p>{{ $subscription->description }}</p>
                    @endif

                    <div class="subscription-badges">
                        <span class="badge">
                            {{ $categories[$subscription->category] ?? __('store.na') }}
                        </span>

                        @if(!empty($subscription->type) && isset($types[$subscription->type]))
                            <span class="badge badge-soft">
                                {{ $types[$subscription->type] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="summary-row">
                <span>{{ __('order.unit_price') }}</span>
                @php
                    $hasDiscount = !empty($subscription->discount) && $subscription->discount > 0;
                    $finalPrice  = $hasDiscount ? $subscription->discount : $subscription->price;
                    $discountPct = $hasDiscount
                        ? round(($subscription->price - $subscription->discount) * 100 / $subscription->price, 2)
                        : 0;
                @endphp
                <strong>
                    {{ number_format($finalPrice, 0) }} {{ __('store.da') }}
                    @if($hasDiscount)
                        ({{ $discountPct }}% {{ __('order.off') }})
                    @endif
                </strong>
            </div>

            <div class="summary-row">
                <span>{{ __('order.quantity') }}</span>

                <div class="quantity-control">
                    <button type="button" onclick="changeQty(-1)" aria-label="Decrease quantity">
                        <i class="fa-solid fa-minus"></i>
                    </button>

                    <input type="number" id="quantity" value="1" min="1" readonly>

                    <button type="button" onclick="changeQty(1)" aria-label="Increase quantity">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="total-box">
                <span>{{ __('order.total') }}</span>
                <strong id="total">
                    {{ number_format($finalPrice, 0) }} {{ __('store.da') }}
                </strong>
            </div>

            <button type="submit" class="place-order-btn">
                {{ __('order.place_order') }}
                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
            </button>
        </aside>

    </form>

</main>

<script>
    const unitPrice    = {{ $finalPrice }};
    const currencyLabel = '{{ __('store.da') }}';

    function changeQty(delta) {
        const qtyInput    = document.getElementById('quantity');
        const hiddenQty   = document.getElementById('hidden_quantity');
        const totalEl     = document.getElementById('total');

        const qty = Math.max(1, parseInt(qtyInput.value) + delta);

        qtyInput.value  = qty;
        hiddenQty.value = qty;

        const total = unitPrice * qty;
        totalEl.textContent = total.toLocaleString('en-US') + ' ' + currencyLabel;
    }
</script>

@endsection

