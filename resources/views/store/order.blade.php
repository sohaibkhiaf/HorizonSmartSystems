@extends('layouts.store')

@section('title', 'Place order')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/order.css') }}">
@endsection

@section('content')

<section class="command-page">

    <a href="{{ route('store.index') }}"><i class="fa-solid fa-arrow-left"></i>Back to catalog</a>
    
    <div class="command-container">

        {{-- Command Form --}}
        <div class="command-card">
            <div class="section-head">
                <div class="section-icon">
                    <i class="fas fa-lock"></i>
                </div>

                <div>
                    <h2>Command</h2>
                    <p>Fill in your details to complete your order.</p>
                </div>
            </div>

            <form 
                action="{{ route('store.confirm', ['product' => $product]) }}" 
                method="POST" class="command-form">
                @csrf

                <div class="form-group">
                    <label>Client full name <span>*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required>
                    @error('full_name') <small>{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Phone number <span>*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required>
                    @error('phone') <small>{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email') <small>{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address') }}">
                    @error('address') <small>{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Order notes</label>
                    <textarea name="notes" rows="4">{{ old('notes') }}</textarea>
                    @error('notes') <small>{{ $message }}</small> @enderror
                </div>

                <p class="secure-message">
                    <i class="fas fa-shield-alt"></i>
                    Your details are encrypted and never shared.
                </p>
            </form>
        </div>

        {{-- Order Summary --}}
        <div class="summary-card">
            <h2>Order Summary</h2>

            <div class="product-summary">
                <img src="{{ asset('uploads/' . $product->image) }}" alt="{{ $product->name }}">

                <div class="product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>
                    <span class="category">
                        @if ($product->category == 'games')
                            Jeux
                        @elseif ($product->category == 'gift_cards')
                            Cartes cadeaux
                        @elseif ($product->category == 'software')
                            Logiciels
                        @elseif ($product->category == 'subscriptions')
                            Abonnements
                        @elseif ($product->category == 'in_game_currency')
                            Monnaie virtuelle
                        @elseif ($product->category == 'other')
                            Autre
                        @else
                            N/A
                        @endif
                    </span>

                    <span class="category">
                        @if ($product->type == 'key')
                            Clé
                        @elseif ($product->type == 'account')
                            Compte
                        @elseif ($product->type == 'invitation')
                            Invitation
                        @endif
                    </span>
                    <!-- <span class="category">{{ $product->category ?? 'No category' }}</span> -->
                </div>
            </div>

            <div class="quantity-box">
                <span>Quantity</span>

                <div class="quantity-control">
                    <button type="button" onclick="changeQty(-1)">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" readonly form="orderForm">
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <div class="total-box">
                <span>Total</span>
                <strong id="total">{{ number_format($product->price, 0, ',', ' ') }} DA</strong>
            </div>

            <button type="submit" form="orderForm" class="place-order-btn">
                Place Order
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</section>

<form id="orderForm" action="{{ route('store.confirm', ['product' => $product]) }}" method="POST" hidden>
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="full_name" id="hidden_full_name">
    <input type="hidden" name="phone" id="hidden_phone">
    <input type="hidden" name="email" id="hidden_email">
    <input type="hidden" name="address" id="hidden_address">
    <input type="hidden" name="notes" id="hidden_notes">
    <input type="hidden" name="quantity" id="hidden_quantity">
</form>

<script>
const productPrice = {{ $product->price }};

function changeQty(value) {
    const quantityInput = document.getElementById('quantity');
    const total = document.getElementById('total');

    let quantity = parseInt(quantityInput.value) + value;
    if (quantity < 1) quantity = 1;

    quantityInput.value = quantity;
    total.textContent = new Intl.NumberFormat('fr-DZ').format(productPrice * quantity) + ' DA';
}

document.querySelector('.place-order-btn').addEventListener('click', function () {
    document.getElementById('hidden_full_name').value = document.querySelector('[name="full_name"]').value;
    document.getElementById('hidden_phone').value = document.querySelector('[name="phone"]').value;
    document.getElementById('hidden_email').value = document.querySelector('[name="email"]').value;
    document.getElementById('hidden_address').value = document.querySelector('[name="address"]').value;
    document.getElementById('hidden_notes').value = document.querySelector('[name="notes"]').value;
    document.getElementById('hidden_quantity').value = document.querySelector('[name="quantity"]').value;

});
</script>
@endsection

