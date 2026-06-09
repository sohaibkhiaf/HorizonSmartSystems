@extends('layouts.store')

@section('title', __('store.title'))

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
        placeholder="{{ __('store.search_placeholder') }}" 
        style="{{ app()->getLocale() == 'ar' ? 'border-left: none; border-radius: 0 12px 12px 0;' : 'border-right: none; border-radius: 12px 0 0 12px;' }}"
        value="{{ request('search') }}">

    @if (!empty($search))
        <a href="{{ route('store.index', ['category' => $category]) }}" type="submit"
            style="{{ app()->getLocale() == 'ar' ? 'border-right: 1px solid #ddd;' : 'border-left: 1px solid #ddd;' }}">
            {{ __('store.reset') }} <i class="fa-solid fa-arrow-rotate-right"></i>
        </a>
    @endif

    <button type="submit"
        style="{{ app()->getLocale() == 'ar' ? 'border-radius: 12px 0 0 12px;' : 'border-radius: 0 12px 12px 0;' }}">
        {{ __('store.search') }} <i class="fa fa-search"></i>
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
                {{ __('store.categories.all') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'games', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'games' ? 'active' : '' }}">
                <i class="fa-solid fa-gamepad"></i>
                {{ __('store.categories.games') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'gift_cards', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'gift_cards' ? 'active' : '' }}">
                <i class="fa-solid fa-gift"></i>
                {{ __('store.categories.gift_cards') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'software', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'software' ? 'active' : '' }}">
                <i class="fa-solid fa-laptop-code"></i>
                {{ __('store.categories.software') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'subscriptions', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'subscriptions' ? 'active' : '' }}">
                <i class="fa-solid fa-repeat"></i>
                {{ __('store.categories.subscriptions') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'in_game_currency', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'in_game_currency' ? 'active' : '' }}">
                <i class="fa-solid fa-coins"></i>
                {{ __('store.categories.in_game_currency') }}
            </a>

            <a href="{{ route('store.index', ['category' => 'other', 'search'=> $search]) }}"
            class="tab {{ request('category') == 'other' ? 'active' : '' }}">
                <i class="fa-solid fa-ellipsis"></i>
                {{ __('store.categories.other') }}
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
                                {{ $product->price }} {{ __('store.da') }}
                            </span>
                        </div>

                        <div class="product-meta">
                            <span class="badge">
                                @if ($product->category == 'games')
                                    {{ __('store.categories.games') }}
                                @elseif ($product->category == 'gift_cards')
                                    {{ __('store.categories.gift_cards') }}
                                @elseif ($product->category == 'software')
                                    {{ __('store.categories.software_plural') }}
                                @elseif ($product->category == 'subscriptions')
                                    {{ __('store.categories.subscriptions') }}
                                @elseif ($product->category == 'in_game_currency')
                                    {{ __('store.categories.in_game_currency') }}
                                @elseif ($product->category == 'other')
                                    {{ __('store.categories.other') }}
                                @else
                                    {{ __('store.na') }}
                                @endif
                            </span>

                            <span class="badge">
                                @if ($product->type == 'key')
                                    {{ __('store.types.key') }}
                                @elseif ($product->type == 'account')
                                    {{ __('store.types.account') }}
                                @elseif ($product->type == 'invitation')
                                    {{ __('store.types.invitation') }}
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

                    <h3>{{ __('store.no_results_title') }}</h3>
                    <p>{{ __('store.no_results_text') }}</p>
                </div>
            @endforelse
        </div>
        
        <!-- Navigator (UNCHANGED) -->
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
