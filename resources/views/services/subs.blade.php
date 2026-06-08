@extends('layouts.app')

@section('title', 'Software subscriptions')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/subs.css') }}">
@endsection

@section('content')

<!-- Hero  -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">

            <h1 class="hero-title">
                Every digital key, delivered smart.
            </h1>

            <p class="hero-description">
                Games, gift cards, software & subscriptions — synced daily, priced fairly.
            </p>

            <div class="hero-buttons">
                <a href="#" class="btn btn-primary" id="shopNowBtn">Shop now &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i></a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hero_image_realisations.png') }}" alt="Hero Image">
        </div>
    </div>
</section>

<!-- Products  -->
<section class="products-tabs-section">

    <div class="tabs-box">
        <div class="tabs">
            <div class="tab active" data-category="all">
                <i class="fa-solid fa-layer-group"></i>
                All
            </div>

            <div class="tab" data-category="games">
                <i class="fa-solid fa-gamepad"></i>
                Jeux
            </div>

            <div class="tab" data-category="gift_cards">
                <i class="fa-solid fa-gift"></i>
                Cartes cadeaux
            </div>

            <div class="tab" data-category="software">
                <i class="fa-solid fa-laptop-code"></i>
                Logiciel
            </div>

            <div class="tab" data-category="subscriptions">
                <i class="fa-solid fa-repeat"></i>
                Abonnements
            </div>

            <div class="tab" data-category="in_game_currency">
                <i class="fa-solid fa-coins"></i>
                Monnaie virtuelle
            </div>

            <div class="tab" data-category="other">
                <i class="fa-solid fa-ellipsis"></i>
                Autre
            </div>
        </div>
    </div>


    <section class="trending">
        <div class="trending-header">
            <h2 class="trending-title">Trending now</h2>
        </div>

        <div class="swiper trending-swiper">
            <div class="swiper-wrapper">

                @foreach($products as $product)
                    <div class="trending-card"
                        data-category="{{ \Illuminate\Support\Str::slug($product->category) }}"
                        data-type="{{ \Illuminate\Support\Str::slug($product->type) }}">

                        <div class="trending-image">
                            @if($product->image)
                                <img src="{{ asset('uploads/' . $product->image) }}"
                                    alt="{{ $product->name }}">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </div>

                        <div class="trending-content">

                            <div class="trending-header">
                                <h3>{{ $product->name }}</h3>

                                <span class="trending-price">
                                    {{ number_format($product->price, 0, ',', ' ') }} DA
                                </span>
                            </div>

                            <div class="trending-meta">
                                <span class="badge">
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

                                <span class="badge">
                                    @if ($product->type == 'key')
                                        Clé
                                    @elseif ($product->type == 'account')
                                        Compte
                                    @elseif ($product->type == 'invitation')
                                        Invitation
                                    @endif
                                </span>
                            </div>

                            <p>
                                {{ $product->description }}
                            </p>

                            <a href="#"
                                class="view-link">
                                Order
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <div class="products-box">
        <div class="products-grid">

            @foreach($products as $product)
                <div class="product-card"
                     data-category="{{ \Illuminate\Support\Str::slug($product->category) }}"
                     data-type="{{ \Illuminate\Support\Str::slug($product->type) }}">

                    <div class="product-image">
                        @if($product->image)
                            <img src="{{ asset('uploads/' . $product->image) }}"
                                 alt="{{ $product->name }}">
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-box"></i>
                            </div>
                        @endif
                    </div>

                    <div class="product-content">

                        <div class="product-header">
                            <h3>{{ $product->name }}</h3>

                            <span class="product-price">
                                {{ number_format($product->price, 0, ',', ' ') }} DA
                            </span>
                        </div>

                        <div class="product-meta">
                            <span class="badge">
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

                            <span class="badge">
                                @if ($product->type == 'key')
                                    Clé
                                @elseif ($product->type == 'account')
                                    Compte
                                @elseif ($product->type == 'invitation')
                                    Invitation
                                @endif
                            </span>
                        </div>

                        <p>
                            {{ $product->description }}
                        </p>

                        <a href="#"
                            class="view-link">
                            Order
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>

                    </div>
                </div>
            @endforeach

        </div>
    </div>

</section>


@endsection

@section('script')
    <script type="module" src="{{ asset('js/subs.js') }}"></script>
@endsection

