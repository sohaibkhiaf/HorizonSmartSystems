@extends('layouts.app')

@section('title', __('home.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<!-- Hero  -->
<section class="hero">
    <div class="hero-container">

        <!-- Left Side -->
        <div class="hero-text">
            <p class="hero-small">{{ __('home.hero_agency_label') }}</p>

            <h1 class="hero-title">
                {{ __('home.hero_title') }}
            </h1>

            <p class="hero-description">
                {{ __('home.hero_description') }}
            </p>

            <div class="hero-buttons">
                <a href="/services" class="btn btn-primary">{{ __('home.hero_btn_services') }} &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i></a>
                <a href="/contact" class="btn btn-outline">{{ __('home.hero_btn_contact') }} &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i></a>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hero-image">
            <img src="{{ asset('images/hero_image.png') }}" alt="Hero Image">
        </div>
    </div>
</section>

<!-- About  -->
<section class="about">
    <div class="about-container">

        <!-- Left Content -->
        <div class="about-text">
            <h2 class="about-title">{{ __('home.about_title') }}</h2>

            <p class="about-description">
                {{ __('home.about_description') }}
            </p>
        </div>

        <!-- Right Cards -->
        <div class="about-cards">

            <!-- Innovation Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-lightbulb"></i>
                    </div>

                    <div class="card-text">
                        <h3>{{ __('home.about_innovation') }}</h3>
                        <p>{{ __('home.about_innovation_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- Performance Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.about_performance') }}</h3>
                        <p>{{ __('home.about_performance_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- Confiance Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.about_trust') }}</h3>
                        <p>{{ __('home.about_trust_desc') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Proposed Services  -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">{{ __('home.services_title') }}</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-globe"></i></div>
                    <h3>{{ __('home.service_web_title') }}</h3>
                    <p>{{ __('home.service_web_desc') }}</p>
                    <a href="/services/webdev" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>{{ __('home.service_app_title') }}</h3>
                    <p>{{ __('home.service_app_desc') }}</p>
                    <a href="/services/appdev" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <h3>{{ __('home.service_erp_title') }}</h3>
                    <p>{{ __('home.service_erp_desc') }}</p>
                    <a href="/services/erp" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-cogs"></i></div>
                    <h3>{{ __('home.service_sys_title') }}</h3>
                    <p>{{ __('home.service_sys_desc') }}</p>
                    <a href="/services/systems" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-headset"></i></div>
                    <h3>{{ __('home.service_support_title') }}</h3>
                    <p>{{ __('home.service_support_desc') }}</p>
                    <a href="/services/support" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Other Services  -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">{{ __('home.other_services_title') }}</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-earth-europe"></i></div>
                    <h3>{{ __('home.other_intl_title') }}</h3>
                    <p>{{ __('home.other_intl_desc') }}</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                    <h3>{{ __('home.other_subscriptions_title') }}</h3>
                    <p>{{ __('home.other_subscriptions_desc') }}</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>{{ __('home.other_hosting_title') }}</h3>
                    <p>{{ __('home.other_hosting_desc') }}</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-circle-info"></i></div>
                    <h3>{{ __('home.other_consulting_title') }}</h3>
                    <p>{{ __('home.other_consulting_desc') }}</p>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Features  -->
<section class="features">
    <div class="features-container">

        <!-- Left Content -->
        <div class="features-left">
            <h2 class="features-title">{{ __('home.features_title') }}</h2>
        </div>

        <!-- Right Cards -->
        <div class="features-right">

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.feature_custom_title') }}</h3>
                        <p>{{ __('home.feature_custom_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.feature_quality_title') }}</h3>
                        <p>{{ __('home.feature_quality_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.feature_speed_title') }}</h3>
                        <p>{{ __('home.feature_speed_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('home.feature_support_title') }}</h3>
                        <p>{{ __('home.feature_support_desc') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Achievements  -->
<section class="acheivements">
    <div class="acheivements-header">
        <h2 class="acheivements-title">{{ __('home.achievements_title') }}</h2>

        <a href="/achievements" class="acheivements-link">
            {{ __('home.achievements_link') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
        </a>
    </div>

    <div class="swiper acheivements-swiper">
        <div class="swiper-wrapper">

            @foreach($achievements as $achievement)
                <div class="swiper-slide acheivement-card">
                    <div class="acheivement-image">
                        @if($achievement->image)
                            <img src="{{ asset('uploads/' . $achievement->image) }}" alt="{{ $achievement->title }}">
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <div class="acheivement-content">
                        <div class="acheivement-header">
                            <h3>{{ $achievement->title }}</h3>
                            <span class="type">{{ $achievement->type }}</span>
                        </div>
                        <p>{{ $achievement->short_description }}</p>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>

<!-- Call To Action -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <!-- LEFT -->
            <div class="cta-left">
                <h2>{{ __('home.cta_title') }}</h2>
                <h3>{{ __('home.cta_subtitle') }}</h3>
            </div>

            <!-- CENTER -->
            <div class="cta-center">
                <p>{{ __('home.cta_description') }}</p>
            </div>

            <!-- RIGHT -->
            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('home.cta_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection


@section('script')
    <script type="module" src="{{ asset('js/home.js') }}"></script>
@endsection

