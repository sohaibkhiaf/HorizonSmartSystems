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

    <!-- Search + Categories Combined -->
    <section class="store-search-section">
        <form method="GET" action="{{ route('store.index') }}" class="search-form">

            <!-- Search Input -->
            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $currentSearch }}"
                    placeholder="{{ __('store.search_placeholder') }}"
                    autocomplete="off">
            </div>

            <!-- Categories Dropdown -->
            <div class="search-category">
                <select name="category">
                    @foreach($categories as $key => $item)
                        <option value="{{ $key }}" 
                            {{ $currentCategory === $key || ($key === 'all' && empty(request('category'))) ? 'selected' : '' }}>
                            {{ $item['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="search-actions">
                @if (!empty($currentSearch))
                    <a href="{{ route('store.index') }}" class="btn-reset">
                        <i class="fa-solid fa-rotate"></i>
                    </a>
                @endif

                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>

        </form>
    </section>

    <!-- Products -->
    <section class="products-tabs-section">

        <!-- Products Box -->
        <div class="products-box">

            <div class="products-grid">
                @forelse($subscriptions as $subscription)
                    <a
                        class="product-card"
                        href="{{ route('store.order', ['subscription' => $subscription]) }}"
                        data-category="{{ \Illuminate\Support\Str::slug($subscription->category ?? '') }}"
                        data-type="{{ \Illuminate\Support\Str::slug($subscription->type ?? '') }}"
                        data-id="{{ $subscription->id }}">

                        <div class="product-image">
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

                        <div class="product-content">

                            <div class="product-header">
                                <h3>{{ $subscription->name }}</h3>

                                <span class="product-price">
                                    {{ $subscription->price }} {{ __('store.da') }}
                                </span>
                            </div>

                            <div class="product-meta">
                                <span class="badge">
                                    {{ $categories[$subscription->category]['label'] ?? __('store.na') }}
                                </span>

                                @if(!empty($subscription->type) && isset($types[$subscription->type]))
                                    <span class="badge badge-soft">
                                        {{ $types[$subscription->type] }}
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

            @if($subscriptions->lastPage() > 1)
                <div class="navigator-container">

                    <a
                        href="{{ $subscriptions->appends(['category' => $currentCategory, 'search' => $currentSearch])->previousPageUrl() ?: '#' }}"
                        class="{{ $subscriptions->onFirstPage() ? 'disabled' : '' }}">
                        <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                    </a>

                    @for ($i = 1; $i <= $subscriptions->lastPage(); $i++)
                        <a
                            href="{{ $subscriptions->appends(['category' => $currentCategory, 'search' => $currentSearch])->url($i) }}"
                            class="{{ $subscriptions->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    <a
                        href="{{ $subscriptions->appends(['category' => $currentCategory, 'search' => $currentSearch])->nextPageUrl() ?: '#' }}"
                        class="{{ $subscriptions->hasMorePages() ? '' : 'disabled' }}">
                        <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                    </a>

                </div>
            @endif

        </div>

    </section>

</main>

@endsection
