@extends('layouts.app')

@section('title', __('achievements.page_title'))

@section('style')
<link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">

            <h1 class="hero-title">
                {{ __('achievements.hero_title') }}
            </h1>

            <p class="hero-description">
                {{ __('achievements.hero_description') }}
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">{{ __('achievements.hero_btn_contact') }} &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i></a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hero_image_realisations.png') }}" alt="Hero Image">
        </div>
    </div>
</section>


<!-- Features Section -->
<section class="features">
    <div class="features-container">

        <!-- Left Content -->
        <div class="features-left">
            <h2 class="features-title">{{ __('achievements.features_title') }}</h2>

            <p class="features-description">
                {{ __('achievements.features_description') }}
            </p>
        </div>

        <!-- Right Cards -->
        <div class="features-right">

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('achievements.feature_web_title') }}</h3>
                        <p>{{ __('achievements.feature_web_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('achievements.feature_ecom_title') }}</h3>
                        <p>{{ __('achievements.feature_ecom_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('achievements.feature_sys_title') }}</h3>
                        <p>{{ __('achievements.feature_sys_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('achievements.feature_support_title') }}</h3>
                        <p>{{ __('achievements.feature_support_desc') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Projects Section -->
<section class="projects-tabs-section">

    <div class="tabs-box">
        <div class="tabs">
            <div class="tab active" data-type="all">
                <i class="fa-solid fa-layer-group"></i>
                {{ __('achievements.tab_all') }}
            </div>

            <div class="tab" data-type="web">
                <i class="fa-solid fa-globe"></i>
                {{ __('achievements.tab_web') }}
            </div>

            <div class="tab" data-type="app">
                <i class="fa-solid fa-code"></i>
                {{ __('achievements.tab_app') }}
            </div>

            <div class="tab" data-type="software">
                <i class="fa-solid fa-laptop-code"></i>
                {{ __('achievements.tab_software') }}
            </div>
        </div>
    </div>

    <div class="projects-box">
        <div class="projects-grid">

            @forelse($achievements as $achievement)
                <div class="acheivement-card" data-type="{{ \Illuminate\Support\Str::slug($achievement->type ?: 'all') }}">
                    <div class="acheivement-image">
                        <img src="{{ $achievement->image ? asset('storage/' . $achievement->image) : asset('images/achievement_1.jpg') }}" alt="{{ $achievement->title }}">
                    </div>
                    <div class="acheivement-content">
                        <div class="acheivement-header">
                            <h3>{{ $achievement->title }}</h3>
                            @if($achievement->type)
                                <span class="type">{{ $achievement->type }}</span>
                            @endif
                        </div>
                        <p>{{ $achievement->short_description }}</p>
                        @if($achievement->project_url)
                            <a class="view-link" href="{{ $achievement->project_url }}" target="_blank" rel="noopener">
                                {{ __('achievements.view_project') }}
                                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="acheivement-card" data-type="web">
                    <div class="acheivement-image">
                        <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
                    </div>
                    <div class="acheivement-content">
                        <div class="acheivement-header">
                            <h3>{{ __('achievements.project_vitrine_title') }}</h3>
                            <span class="type">{{ __('achievements.type_web') }}</span>
                        </div>
                        <p>{{ __('achievements.project_vitrine_desc') }}</p>
                        <a class="view-link">
                            {{ __('achievements.view_project') }}
                            <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>

                <div class="acheivement-card" data-type="app">
                    <div class="acheivement-image">
                        <img src="{{ asset('images/achievement_2.webp') }}" alt="">
                    </div>
                    <div class="acheivement-content">
                        <div class="acheivement-header">
                            <h3>{{ __('achievements.project_app_title') }}</h3>
                            <span class="type">{{ __('achievements.type_app') }}</span>
                        </div>
                        <p>{{ __('achievements.project_app_desc') }}</p>
                        <a class="view-link">
                            {{ __('achievements.view_project') }}
                            <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>

                <div class="acheivement-card" data-type="software">
                    <div class="acheivement-image">
                        <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
                    </div>
                    <div class="acheivement-content">
                        <div class="acheivement-header">
                            <h3>{{ __('achievements.project_erp_title') }}</h3>
                            <span class="type">{{ __('achievements.type_erp') }}</span>
                        </div>
                        <p>{{ __('achievements.project_erp_desc') }}</p>
                        <a class="view-link">
                            {{ __('achievements.view_project') }}
                            <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                        </a>
                    </div>
                </div>
            @endforelse

        </div>
    </div>

</section>


<!-- Call To Action Section -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <!-- LEFT -->
            <div class="cta-left">
                <h2>{{ __('achievements.cta_title') }}</h2>
                <h3>{{ __('achievements.cta_subtitle') }}</h3>
            </div>

            <!-- RIGHT -->
            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('achievements.cta_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection

@section('script')
    <script type="module" src="{{ asset('js/achievements.js') }}"></script>
@endsection

