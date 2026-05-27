@extends('layouts.app')

@section('title', __('services.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
@endsection


@section('content')

<!-- Hero  -->
<section class="hero">
    <div class="hero-container">

        <!-- Left Side -->
        <div class="hero-text">

            <h1 class="hero-title">
                {{ __('services.hero_title') }}
            </h1>

            <p class="hero-description">
                {{ __('services.hero_description') }}
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">{{ __('services.hero_btn_contact') }} &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i></a>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hero-image">
            <img src="{{ asset('images/hero_image_nos_services.png') }}" alt="Hero Image">
        </div>
    </div>
</section>


<!-- Proposed Services  -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">{{ __('services.main_services_title') }}</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-globe"></i></div>
                    <h3>{{ __('services.service_web_title') }}</h3>
                    <p>{{ __('services.service_web_desc') }}</p>
                    <a href="/services/webdev" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>{{ __('services.service_app_title') }}</h3>
                    <p>{{ __('services.service_app_desc') }}</p>
                    <a href="/services/appdev" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-chart-pie"></i></div>
                    <h3>{{ __('services.service_erp_title') }}</h3>
                    <p>{{ __('services.service_erp_desc') }}</p>
                    <a href="/services/erp" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-cogs"></i></div>
                    <h3>{{ __('services.service_sys_title') }}</h3>
                    <p>{{ __('services.service_sys_desc') }}</p>
                    <a href="/services/systems" class="plus-link">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-headset"></i></div>
                    <h3>{{ __('services.service_support_title') }}</h3>
                    <p>{{ __('services.service_support_desc') }}</p>
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

        <h2 class="services-title">{{ __('services.other_services_title') }}</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-earth-europe"></i></div>
                    <h3>{{ __('services.other_intl_title') }}</h3>
                    <p>{{ __('services.other_intl_desc') }}</p>
                    <a href="#" class="plus-link" style="display:none;">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                    <h3>{{ __('services.other_subscriptions_title') }}</h3>
                    <p>{{ __('services.other_subscriptions_desc') }}</p>
                    <a href="#" class="plus-link" style="display:none;">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>{{ __('services.other_hosting_title') }}</h3>
                    <p>{{ __('services.other_hosting_desc') }}</p>
                    <a href="#" class="plus-link" style="display:none;">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-circle-info"></i></div>
                    <h3>{{ __('services.other_consulting_title') }}</h3>
                    <p>{{ __('services.other_consulting_desc') }}</p>
                    <a href="#" class="plus-link" style="display:none;">
                        {{ __('services.learn_more') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>


<!-- Method  -->
<section class="method">
    <div class="method-container">

        <div class="method-left">
            <h2 class="method-title">{{ __('services.method_title') }}</h2>

            <p class="method-description">
                {{ __('services.method_description') }}
            </p>
        </div>

        <div class="method-right">

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-1"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('services.method_step1_title') }} <i class="fa-solid fa-magnifying-glass"></i></h3>
                        <p>{{ __('services.method_step1_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-2"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('services.method_step2_title') }} <i class="fa-regular fa-pen-to-square"></i></h3>
                        <p>{{ __('services.method_step2_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-3"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('services.method_step3_title') }} <i class="fa-solid fa-code"></i></h3>
                        <p>{{ __('services.method_step3_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-4"></i>
                    </div>
                    <div class="card-text">
                        <h3>{{ __('services.method_step4_title') }} <i class="fa-solid fa-rocket"></i></h3>
                        <p>{{ __('services.method_step4_desc') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Call To Action  -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>{{ __('services.cta_title') }}</h2>
                <h3>{{ __('services.cta_subtitle') }}</h3>
            </div>

            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('services.cta_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection

@section('script')
    <script type="module" src="{{ asset('js/services.js') }}"></script>
@endsection

