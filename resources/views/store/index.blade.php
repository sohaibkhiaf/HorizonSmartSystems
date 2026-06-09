@extends('layouts.store')

@section('title', 'Store')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/index.css') }}">
@endsection

@section('content')

<!-- Search Form -->
<form method="GET" action="{{ route('store.index') }}" class="search-form">

    <input type="hidden" name="category" value="{{ $category }}">

    <input 
        type="text" 
        name="search" 
        placeholder="Search products..." 
        value="{{ request('search') }}">

    @if (!empty($search))
        <a href="{{ route('store.index', ['category' => $category]) }}" type="submit">
            Réinit <i class="fa-solid fa-arrow-rotate-right"></i>
        </a>
    @endif

    <button type="submit">
        Search <i class="fa fa-search"></i>
    </button>

    
</form>

<!-- Products Section -->
<section class="products-tabs-section">

    <!-- Tabs -->
    <div class="tabs-box">
        <div class="tabs">

            <a href="{{ route('store.index', ['category' => 'all', 'search'=> $search]) }}"
            class="tab {{ request('category', 'all') == 'all' ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                All
            </a>

            <a href="{{ route('store.index', ['category' => 'games', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'games' ? 'active' : '' }}">
                <i class="fa-solid fa-gamepad"></i>
                Jeux
            </a>

            <a href="{{ route('store.index', ['category' => 'gift_cards', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'gift_cards' ? 'active' : '' }}">
                <i class="fa-solid fa-gift"></i>
                Cartes cadeaux
            </a>

            <a href="{{ route('store.index', ['category' => 'software', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'software' ? 'active' : '' }}">
                <i class="fa-solid fa-laptop-code"></i>
                Logiciel
            </a>

            <a href="{{ route('store.index', ['category' => 'subscriptions', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'subscriptions' ? 'active' : '' }}">
                <i class="fa-solid fa-repeat"></i>
                Abonnements
            </a>

            <a href="{{ route('store.index', ['category' => 'in_game_currency', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'in_game_currency' ? 'active' : '' }}">
                <i class="fa-solid fa-coins"></i>
                Monnaie virtuelle
            </a>

            <a href="{{ route('store.index', ['category' => 'other', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'other' ? 'active' : '' }}">
                <i class="fa-solid fa-ellipsis"></i>
                Autre
            </a>
        </div>
    </div>

    <!-- Products Box -->
    <div class="products-box">
        <div class="products-grid">
            @forelse($products as $product)
                <a class="product-card"
                    href="{{ route('store.order', ['product' => $product ]) }}"
                    data-category="{{ \Illuminate\Support\Str::slug($product->category) }}"
                    data-type="{{ \Illuminate\Support\Str::slug($product->type) }}"
                    data-id="{{ $product->id }}">

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

                    </div>
                </a>
            @empty
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>

                    <h3>No results found</h3>
                    <p>Try adjusting your search or filters to find what you're looking for.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Navigator -->
        <div class="navigator-container">
            @if ($products->currentPage() > 1)
                <a href="{{$products->previousPageUrl().'&category='.$category.'&search='.$search}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
            @else
                <a href="{{$products->url($products->currentPage()).'&category='.$category.'&search='.$search}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
            @endif

            @for ($i = 1; $i <= $products->lastPage(); $i++)
                @if ($products->currentPage() == $i)
                    <a href="{{$products->url($i).'&category='.$category.'&search='.$search}}" class="active">{{$i}}</a>
                @else
                    <a href="{{$products->url($i).'&category='.$category.'&search='.$search}}">{{$i}}</a>
                @endif
            @endfor

            @if ($products->currentPage() < $products->lastPage())
                <a href="{{$products->nextPageUrl().'&category='.$category.'&search='.$search}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
            @else
                <a href="{{$products->url($products->currentPage()).'&category='.$category.'&search='.$search}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
            @endif
        </div>
    </div>

</section>

@endsection

