@extends('layouts.store')

@section('title', __('store.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/index.css') }}">
@endsection

@section('content')

@php
    $currentCategory = request('category', $category ?? 'all');
    $currentSearch = request('search', $search ?? '');

    $categories = [
        'all' => [
            'label' => __('store.categories.all'),
            'icon' => 'fa-solid fa-layer-group',
        ],
        'games' => [
            'label' => __('store.categories.games'),
            'icon' => 'fa-solid fa-gamepad',
        ],
        'gift_cards' => [
            'label' => __('store.categories.gift_cards'),
            'icon' => 'fa-solid fa-gift',
        ],
        'software' => [
            'label' => __('store.categories.software'),
            'icon' => 'fa-solid fa-laptop-code',
        ],
        'subscriptions' => [
            'label' => __('store.categories.subscriptions'),
            'icon' => 'fa-solid fa-repeat',
        ],
        'in_game_currency' => [
            'label' => __('store.categories.in_game_currency'),
            'icon' => 'fa-solid fa-coins',
        ],
        'other' => [
            'label' => __('store.categories.other'),
            'icon' => 'fa-solid fa-ellipsis',
        ],
    ];

    $types = [
        'key' => __('store.types.key'),
        'account' => __('store.types.account'),
        'invitation' => __('store.types.invitation'),
    ];
@endphp

<main class="store-page {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    <!-- Search -->
    <section class="store-search-section">
        <form method="GET" action="{{ route('store.index') }}" class="search-form">
            <input type="hidden" name="category" value="{{ $currentCategory }}">

            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $currentSearch }}"
                    placeholder="{{ __('store.search_placeholder') }}"
                    autocomplete="off">
            </div>

            <div class="search-actions">
                @if (!empty($currentSearch))
                    <a href="{{ route('store.index', ['category' => $currentCategory]) }}" class="btn-reset">
                        {{ __('store.reset') }}
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                    </a>
                @endif

                <button type="submit" class="btn-search">
                    {{ __('store.search') }}
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>
        </form>
    </section>

    <!-- Products -->
    <section class="products-tabs-section">

        <!-- Categories -->
        <div class="tabs-box">
            <div class="tabs" aria-label="Product categories">
                @foreach($categories as $key => $item)
                    <a
                        href="{{ route('store.index', ['category' => $key, 'search' => $currentSearch]) }}"
                        class="tab {{ $currentCategory === $key || ($key === 'all' && empty(request('category'))) ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Box -->
        <div class="products-box">

            <div class="products-grid">
                @forelse($products as $product)
                    <a
                        class="product-card"
                        href="{{ route('store.order', ['product' => $product]) }}"
                        data-category="{{ \Illuminate\Support\Str::slug($product->category ?? '') }}"
                        data-type="{{ \Illuminate\Support\Str::slug($product->type ?? '') }}"
                        data-id="{{ $product->id }}">

                        <div class="product-image">
                            @if($product->image)
                                <img
                                    src="{{ asset('uploads/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa-solid fa-box-open"></i>
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
                                    {{ $categories[$product->category]['label'] ?? __('store.na') }}
                                </span>

                                @if(!empty($product->type) && isset($types[$product->type]))
                                    <span class="badge badge-soft">
                                        {{ $types[$product->type] }}
                                    </span>
                                @endif
                            </div>

                            <span class="order-btn">
                                {{ __('store.order_now') }}
                                <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                            </span>

                        </div>
                    </a>
                @empty
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>

                        <h3>{{ __('store.no_results_title') }}</h3>
                        <p>{{ __('store.no_results_text') }}</p>

                        <a href="{{ route('store.index', ['category' => 'all']) }}" class="no-results-link">
                            {{ __('store.reset') }}
                            <i class="fa-solid fa-arrow-rotate-right"></i>
                        </a>
                    </div>
                @endforelse
            </div>

            @if($products->lastPage() > 1)
                <div class="navigator-container">

                    <a
                        href="{{ $products->appends(['category' => $currentCategory, 'search' => $currentSearch])->previousPageUrl() ?: '#' }}"
                        class="{{ $products->onFirstPage() ? 'disabled' : '' }}">
                        <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                    </a>

                    @for ($i = 1; $i <= $products->lastPage(); $i++)
                        <a
                            href="{{ $products->appends(['category' => $currentCategory, 'search' => $currentSearch])->url($i) }}"
                            class="{{ $products->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    <a
                        href="{{ $products->appends(['category' => $currentCategory, 'search' => $currentSearch])->nextPageUrl() ?: '#' }}"
                        class="{{ $products->hasMorePages() ? '' : 'disabled' }}">
                        <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                    </a>

                </div>
            @endif

        </div>

    </section>

</main>

@endsection
