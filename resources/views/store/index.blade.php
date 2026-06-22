@extends('layouts.store')

@section('title', __('store.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/store/index.css') }}">
@endsection

@section('content')

@php
    $currentCategory = request('category', $category ?? 'all');
    $currentSearch = request('search', $search ?? '');

    $locale = app()->getLocale();

    $ui = [
        'fr' => [
            'kicker' => 'Boutique Horizon PC',
            'intro' => 'Trouvez rapidement vos jeux, cartes cadeaux, logiciels et abonnements avec une commande simple et rapide.',
            'products' => 'Produits',
            'categories' => 'Catégories',
            'offers' => 'Offres',
            'quick_categories' => 'Catégories rapides',
            'bestsellers_title' => 'Meilleures ventes',
            'bestsellers_text' => 'Les produits les plus demandés par nos clients.',
            'products_title' => 'Tous les produits',
            'search_title' => 'Résultats de recherche',
            'category_title' => 'Produits de la catégorie',
            'all_text' => 'Parcourez tous les produits disponibles dans la boutique.',
            'search_text' => 'Produits correspondant à votre recherche.',
            'category_text' => 'Produits filtrés selon la catégorie sélectionnée.',
            'result' => 'produit(s)',
            'fast_order' => 'Commande rapide',
            'trusted' => 'Boutique Horizon',
            'view_product' => 'Voir le produit',
            'best' => 'Best seller',
        ],
        'ar' => [
            'kicker' => 'متجر Horizon PC',
            'intro' => 'اختر الألعاب، بطاقات الهدايا، البرامج والاشتراكات واطلبها بسرعة وسهولة.',
            'products' => 'منتجات',
            'categories' => 'أقسام',
            'offers' => 'عروض',
            'quick_categories' => 'الأقسام',
            'bestsellers_title' => 'الأكثر طلبا',
            'bestsellers_text' => 'المنتجات التي يطلبها العملاء بكثرة.',
            'products_title' => 'كل المنتجات',
            'search_title' => 'نتائج البحث',
            'category_title' => 'منتجات القسم',
            'all_text' => 'تصفح جميع المنتجات المتوفرة في المتجر.',
            'search_text' => 'منتجات مطابقة لبحثك.',
            'category_text' => 'منتجات حسب القسم المحدد.',
            'result' => 'منتج',
            'fast_order' => 'طلب سريع',
            'trusted' => 'متجر Horizon',
            'view_product' => 'عرض المنتج',
            'best' => 'الأكثر طلبا',
        ],
        'en' => [
            'kicker' => 'Horizon PC Store',
            'intro' => 'Find games, gift cards, software and subscriptions with a simple and fast order process.',
            'products' => 'Products',
            'categories' => 'Categories',
            'offers' => 'Offers',
            'quick_categories' => 'Quick categories',
            'bestsellers_title' => 'Bestsellers',
            'bestsellers_text' => 'The products most requested by our customers.',
            'products_title' => 'All products',
            'search_title' => 'Search results',
            'category_title' => 'Category products',
            'all_text' => 'Browse all available products in the store.',
            'search_text' => 'Products matching your search.',
            'category_text' => 'Products filtered by the selected category.',
            'result' => 'product(s)',
            'fast_order' => 'Fast order',
            'trusted' => 'Horizon Store',
            'view_product' => 'View product',
            'best' => 'Best seller',
        ],
    ][$locale] ?? [
        'kicker' => 'Horizon PC Store',
        'intro' => 'Find games, gift cards, software and subscriptions with a simple and fast order process.',
        'products' => 'Products',
        'categories' => 'Categories',
        'offers' => 'Offers',
        'quick_categories' => 'Quick categories',
        'bestsellers_title' => 'Bestsellers',
        'bestsellers_text' => 'The products most requested by our customers.',
        'products_title' => 'All products',
        'search_title' => 'Search results',
        'category_title' => 'Category products',
        'all_text' => 'Browse all available products in the store.',
        'search_text' => 'Products matching your search.',
        'category_text' => 'Products filtered by the selected category.',
        'result' => 'product(s)',
        'fast_order' => 'Fast order',
        'trusted' => 'Horizon Store',
        'view_product' => 'View product',
        'best' => 'Best seller',
    ];

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
        $sectionTitle = $ui['search_title'];
        $sectionText = $ui['search_text'];
    } elseif ($currentCategory !== 'all') {
        $sectionTitle = $ui['category_title'] . ' : ' . ($categories[$currentCategory]['label'] ?? '');
        $sectionText = $ui['category_text'];
    } else {
        $sectionTitle = $ui['products_title'];
        $sectionText = $ui['all_text'];
    }
@endphp

<main class="store-page {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    @php
        $heroImage = asset('images/subscriptions.png');
    @endphp

    <!-- Store Hero -->
    <section
        class="store-hero-section"
        style="--store-hero-image: url('{{ $heroImage }}');">

        <div class="store-hero-overlay"></div>

        <div class="store-hero-inner">

            <div class="store-hero-content">
                <span class="store-kicker">
                    <i class="fa-solid fa-store"></i>
                    {{ $ui['kicker'] }}
                </span>

                <h1>{{ __('store.title') }}</h1>

                <p>
                    {{ $ui['intro'] }}
                </p>

                <div class="store-hero-actions">
                    <a href="#products_section" class="hero-btn hero-btn-primary">
                        {{ $ui['products'] }}
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
                    <span>{{ $ui['products'] }}</span>
                </div>

                <div class="store-highlight">
                    <i class="fa-solid fa-layer-group"></i>
                    <strong>{{ count($categories) - 1 }}</strong>
                    <span>{{ $ui['categories'] }}</span>
                </div>

                <div class="store-highlight">
                    <i class="fa-solid fa-tag"></i>
                    <strong>{{ $discountedCount ?? 0 }}</strong>
                    <span>{{ $ui['offers'] }}</span>
                </div>
            </div>

        </div>
    </section>

    <!-- Quick Categories -->
    <section class="category-pills-section">
        <div class="section-top">
            <div>
                <span>{{ $ui['quick_categories'] }}</span>
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
    <section class="store-search-section"  id="search_section">
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

    <!-- Bestsellers: hidden when search is active -->
    @if(!$hasSearch && isset($bestSellers) && $bestSellers->isNotEmpty())
        <section class="bestsellers-section" data-slider>
            <div class="section-top">
                <div>
                    <span>
                        <i class="fa-solid fa-fire"></i>
                        {{ $ui['best'] }}
                    </span>

                    <h2>{{ $ui['bestsellers_title'] }}</h2>
                    <p>{{ $ui['bestsellers_text'] }}</p>
                </div>
            </div>

            <div class="bestseller-slider-shell">
                <button type="button" class="slider-btn" data-slider-prev>
                    <i class="fa-solid fa-angle-left"></i>
                </button>

                <div class="bestseller-track" data-slider-track>
                    @foreach($bestSellers as $item)
                        @php
                            $hasDiscount = !empty($item->discount) && $item->discount > 0;
                            $finalPrice = $hasDiscount ? $item->discount : $item->price;
                            $discountPercentage = null;

                            if ($hasDiscount && $item->price > 0) {
                                $discountPercentage = round((($item->price - $item->discount) * 100) / $item->price, 2);
                            }
                        @endphp

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
                                        {{ number_format($finalPrice, 0) }} {{ __('store.da') }}
                                    </span>
                                </div>

                                <span class="bestseller-link">
                                    {{ $ui['view_product'] }}
                                    <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                                </span>
                            </div>

                        </a>
                    @endforeach
                </div>

                <button type="button" class="slider-btn" data-slider-next>
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>
        </section>
    @endif

    <!-- Subscriptions -->
    <section class="subscriptions-tabs-section" id="products_section">

        <div class="products-section-header">
            <div>
                <span>{{ __('store.title') }}</span>
                <h2>{{ $sectionTitle }}</h2>
                <p>{{ $sectionText }}</p>
            </div>

            <strong>
                {{ $subscriptions->total() }} {{ $ui['result'] }}
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
                                        {{ number_format($finalPrice, 0) }} {{ __('store.da') }}
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

                            <div class="subscription-details">
                                <span>
                                    <i class="fa-solid fa-bolt"></i>
                                    {{ $ui['fast_order'] }}
                                </span>

                                <span>
                                    <i class="fa-solid fa-shield-halved"></i>
                                    {{ $ui['trusted'] }}
                                </span>
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
    document.querySelectorAll('[data-slider]').forEach((slider) => {
        const track = slider.querySelector('[data-slider-track]');
        const prev = slider.querySelector('[data-slider-prev]');
        const next = slider.querySelector('[data-slider-next]');

        if (!track || !prev || !next) return;

        const getStep = () => Math.max(track.clientWidth * 0.75, 260);

        prev.addEventListener('click', () => {
            track.scrollBy({
                left: -getStep(),
                behavior: 'smooth'
            });
        });

        next.addEventListener('click', () => {
            track.scrollBy({
                left: getStep(),
                behavior: 'smooth'
            });
        });
    });
});
</script>

@endsection
