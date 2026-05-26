@extends('layouts.app')

@section('title', __('appdev.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/services/appdev.css') }}">
@endsection

@section('content')

<!-- Hero -->
<section class="appdev-hero">
    <div class="appdev-hero-container">

        <div class="appdev-hero-text">
            <h1 class="appdev-hero-title">{{ __('appdev.hero_title') }}</h1>
            <p class="appdev-hero-description">{{ __('appdev.hero_description') }}</p>

            <div class="appdev-hero-buttons">
                <a href="/contact" class="appdev-btn appdev-btn-primary">
                    {{ __('appdev.hero_btn_contact') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>
        </div>

        <div class="appdev-hero-image">
            <img src="{{ asset('images/webdev.png') }}" alt="App Development">
        </div>

    </div>
</section>

<!-- Description -->
<section class="appdev-description">
    <div class="appdev-description-container">
        <h2>{{ __('appdev.desc_title') }}</h2>
        <p>{{ __('appdev.desc_text') }}</p>
    </div>
</section>


<!-- Types Section -->
<section class="website-types">

    <!-- Corporate -->
    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>

            <div>
                <h2>{{ __('appdev.mobile_title') }}</h2>
                <p>{{ __('appdev.mobile_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('appdev.corporate_features') }}</h3>

                <ul>
                    @foreach(__('appdev.mobile_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('appdev.price_label') }}</span>
                <h3>{{ __('appdev.mobile_price') }}</h3>
                <p>{{ __('appdev.price_note') }}</p>
            </div>
        </div>
    </div>


    <!-- E-commerce -->
    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>

            <div>
                <h2>{{ __('appdev.web_title') }}</h2>
                <p>{{ __('appdev.web_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('appdev.corporate_features') }}</h3>

                <ul>
                    @foreach(__('appdev.web_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('appdev.price_label') }}</span>
                <h3>{{ __('appdev.web_price') }}</h3>
                <p>{{ __('appdev.price_note') }}</p>
            </div>
        </div>
    </div>

    <!-- E-commerce -->
    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>

            <div>
                <h2>{{ __('appdev.desktop_title') }}</h2>
                <p>{{ __('appdev.desktop_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('appdev.corporate_features') }}</h3>

                <ul>
                    @foreach(__('appdev.desktop_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('appdev.price_label') }}</span>
                <h3>{{ __('appdev.desktop_price') }}</h3>
                <p>{{ __('appdev.price_note') }}</p>
            </div>
        </div>
    </div>

</section>

<!-- CTA -->
<section class="appdev-cta">
    <div class="appdev-cta-overlay">

        <div class="appdev-cta-container">

            <div class="appdev-cta-left">
                <h2>{{ __('appdev.cta_title') }}</h2>
                <h3>{{ __('appdev.cta_subtitle') }}</h3>
            </div>

            <div class="appdev-cta-right">
                <a href="/contact" class="appdev-cta-btn">
                    {{ __('appdev.cta_btn') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection

