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

    $hasSearch = !empty($currentSearch);

    if ($hasSearch) {
        $sectionTitle = __('store.index.search_title');
        $sectionText = __('store.index.search_text');
    } elseif ($currentCategory !== 'all') {
        $sectionTitle = __('store.index.category_title', [
            'category' => $categories[$currentCategory]['label'] ?? '',
        ]);
        $sectionText = __('store.index.category_text');
    } else {
        $sectionTitle = __('store.index.products_title');
        $sectionText = __('store.index.all_text');
    }
@endphp

<main class="store-page {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    @php
        $heroImage = asset('images/store_hero.png');
    @endphp

    <!-- Store Hero -->
    <section
        class="store-hero-section"
        style="--store-hero-image: url('{{ $heroImage }}');">

        <div class="store-hero-inner">

            <div class="store-hero-content">
                <span class="store-kicker">
                    <i class="fa-solid fa-store"></i>
                    {{ __('store.index.kicker') }}
                </span>

                <h1>{{ __('store.title') }}</h1>

                <p>
                    {{ __('store.index.intro') }}
                </p>

                <div class="store-hero-actions">
                    <a href="#products_section" class="hero-btn hero-btn-primary">
                        {{ __('store.index.products') }}
                        <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                    </a>

                    <a href="#search_section" class="hero-btn hero-btn-light">
                        {{ __('store.search_placeholder') }}
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </div>
            </div>

            <div class="store-highlights">
                <div class="store-highlight">
                    <i class="fa-solid fa-box"></i>
                    <strong>{{ $totalSubscriptions ?? 0 }}</strong>
                    <span>{{ __('store.index.products') }}</span>
                </div>

                <div class="store-highlight">
                    <i class="fa-solid fa-layer-group"></i>
                    <strong>{{ count($categories) - 1 }}</strong>
                    <span>{{ __('store.index.categories') }}</span>
                </div>

                <div class="store-highlight">
                    <i class="fa-solid fa-tag"></i>
                    <strong>{{ $discountedCount ?? 0 }}</strong>
                    <span>{{ __('store.index.offers') }}</span>
                </div>
            </div>

        </div>
    </section>

    <!-- Quick Categories -->
    <section class="category-pills-section">
        <div class="section-top">
            <div>
                <span><i class="fa-solid fa-layer-group"></i>{{ __('store.index.quick_categories') }}</span>
            </div>
        </div>

        <div class="category-pills">
            @foreach($categories as $key => $item)
                @php
                    $count = $key === 'all'
                        ? ($totalSubscriptions ?? 0)
                        : ($categoryCounts[$key] ?? 0);
                @endphp

                <a
                    href="{{ route('store.index', ['category' => $key]) }}"
                    class="category-pill {{ $currentCategory === $key || ($key === 'all' && empty(request('category'))) ? 'active' : '' }}">
                    <i class="{{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                    <small>{{ $count }}</small>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Search + Categories Combined -->
    <section class="store-search-section" id="search_section">
        <form method="GET" action="{{ route('store.index') }}" class="search-form">

            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $currentSearch }}"
                    placeholder="{{ __('store.search_placeholder') }}"
                    autocomplete="off">
            </div>

            <div class="search-category">
                <select name="category">
                    @foreach($categories as $key => $item)
                        <option
                            value="{{ $key }}"
                            {{ $currentCategory === $key || ($key === 'all' && empty(request('category'))) ? 'selected' : '' }}>
                            {{ $item['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="search-actions">
                @if (!empty($currentSearch) || $currentCategory !== 'all')
                    <a href="{{ route('store.index') }}" class="btn-reset" aria-label="{{ __('store.reset') }}">
                        <i class="fa-solid fa-rotate"></i>
                    </a>
                @endif

                <button type="submit" class="btn-search" aria-label="{{ __('store.search_placeholder') }}">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>

        </form>
    </section>

    <!-- Bestsellers: hidden when search is active -->
    @if(!$hasSearch && isset($bestSellers) && $bestSellers->isNotEmpty())
        <section class="bestsellers-section">
            <div class="section-top">
                <div>
                    <span>
                        <i class="fa-solid fa-fire"></i>
                        {{ __('store.index.best') }}
                    </span>

                    <h2>{{ __('store.index.bestsellers_title') }}</h2>
                    <p>{{ __('store.index.bestsellers_text') }}</p>
                </div>
            </div>

            <div class="bestseller-slider-shell">
                <button type="button" class="bestseller-nav bestseller-prev" aria-label="{{ __('store.index.previous') }}">
                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                </button>

                <div class="swiper bestseller-swiper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    <div class="swiper-wrapper">
                        @foreach($bestSellers as $item)
                            @php
                                $hasDiscount = !empty($item->discount) && $item->discount > 0;
                                $finalPrice = $hasDiscount ? $item->discount : $item->price;
                                $discountPercentage = null;

                                if ($hasDiscount && $item->price > 0) {
                                    $discountPercentage = round((($item->price - $item->discount) * 100) / $item->price, 2);
                                }
                            @endphp

                            <div class="swiper-slide">
                                <a class="bestseller-card" href="{{ route('store.order', ['subscription' => $item]) }}">

                                    <div class="bestseller-image">
                                        @if($item->image)
                                            <img
                                                src="{{ asset('uploads/' . $item->image) }}"
                                                alt="{{ $item->name }}"
                                                loading="lazy">
                                        @else
                                            <div class="image-placeholder">
                                                <i class="fa-solid fa-box-open"></i>
                                            </div>
                                        @endif

                                        @if($discountPercentage)
                                            <span class="discount-badge" dir="ltr">
                                                -{{ $discountPercentage }}%
                                            </span>
                                        @endif
                                    </div>

                                    <div class="bestseller-content">
                                        <span class="bestseller-category">
                                            {{ $categories[$item->category]['label'] ?? __('store.na') }}
                                        </span>

                                        <h3>{{ $item->name }}</h3>

                                        <div class="subscription-price-wrapper">
                                            @if($hasDiscount)
                                                <span class="old-price">
                                                    {{ $item->price }} {{ __('store.da') }}
                                                </span>
                                            @endif

                                            <span class="subscription-price">
                                                {{ $finalPrice }} {{ __('store.da') }}
                                            </span>
                                        </div>

                                        <span class="bestseller-link">
                                            {{ __('store.index.view_product') }}
                                            <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                                        </span>
                                    </div>

                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="bestseller-nav bestseller-next" aria-label="{{ __('store.index.next') }}">
                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                </button>
            </div>
        </section>
    @endif

    <!-- Subscriptions -->
    <section class="subscriptions-tabs-section" id="products_section">

        <div class="products-section-header">
            <div>
                <span><i class="fa-solid fa-store"></i> {{ __('store.title') }}</span>
                <h2>{{ $sectionTitle }}</h2>
                <p>{{ $sectionText }}</p>
            </div>

            <strong>
                {{ $subscriptions->total() }} {{ __('store.index.result') }}
            </strong>
        </div>

        <div class="subscriptions-box">

            <div class="subscriptions-grid">
                @forelse($subscriptions as $subscription)
                    @php
                        $hasDiscount = !empty($subscription->discount) && $subscription->discount > 0;
                        $finalPrice = $hasDiscount ? $subscription->discount : $subscription->price;
                        $discountPercentage = null;

                        if ($hasDiscount && $subscription->price > 0) {
                            $discountPercentage = round((($subscription->price - $subscription->discount) * 100) / $subscription->price, 2);
                        }
                    @endphp

                    <a
                        class="subscription-card"
                        href="{{ route('store.order', ['subscription' => $subscription]) }}"
                        data-category="{{ \Illuminate\Support\Str::slug($subscription->category ?? '') }}"
                        data-type="{{ \Illuminate\Support\Str::slug($subscription->type ?? '') }}"
                        data-id="{{ $subscription->id }}">

                        <div class="subscription-image">
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

                            @if($discountPercentage)
                                <span class="discount-badge" dir="ltr">
                                    -{{ $discountPercentage }}%
                                </span>
                            @endif
                        </div>

                        <div class="subscription-content">

                            <div class="subscription-header">
                                <h3>{{ $subscription->name }}</h3>

                                <div class="subscription-price-wrapper">
                                    @if($hasDiscount)
                                        <span class="old-price">
                                            {{ $subscription->price }} {{ __('store.da') }}
                                        </span>
                                    @endif

                                    <span class="subscription-price">
                                        {{ $finalPrice }} {{ __('store.da') }}
                                    </span>
                                </div>
                            </div>

                            @if(!empty($subscription->description))
                                <p class="subscription-description">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($subscription->description), 90) }}
                                </p>
                            @endif

                            <div class="subscription-meta">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const bestsellerSlider = document.querySelector('.bestseller-swiper');

    if (!bestsellerSlider || typeof Swiper === 'undefined') return;

    new Swiper(bestsellerSlider, {
        slidesPerView: 1.15,
        spaceBetween: 14,
        grabCursor: true,
        watchOverflow: true,
        centeredSlides: true,
        navigation: {
            nextEl: '.bestseller-next',
            prevEl: '.bestseller-prev',
        },
        breakpoints: {
            560: {
                slidesPerView: 2,
                spaceBetween: 16,
                centeredSlides: false,
            },
            820: {
                slidesPerView: 3,
                spaceBetween: 16,
                centeredSlides: false,
            },
            1100: {
                slidesPerView: 4,
                spaceBetween: 18,
                centeredSlides: false,
            },
            1500: {
                slidesPerView: 5,
                spaceBetween: 18,
                centeredSlides: false,
            },
            1900: {
                slidesPerView: 6,
                spaceBetween: 20,
                centeredSlides: false,
            },
        },
    });
});
</script>

@endsection
