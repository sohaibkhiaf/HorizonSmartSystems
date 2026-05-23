@extends('layouts.app')

@section('title', __('webdev.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/services/webdev.css') }}">
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">
                {{ __('webdev.hero_title') }}
            </h1>

            <p class="hero-description">
                {{ __('webdev.hero_description') }}
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">
                    {{ __('webdev.hero_btn_contact') }} &nbsp;<i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/webdev.png') }}" alt="{{ __('webdev.hero_title') }}">
        </div>

    </div>
</section>


<!-- Description Section -->
<section class="web-description">
    <div class="web-description-container">
        <h2>{{ __('webdev.desc_title') }}</h2>
        <p>{{ __('webdev.desc_text') }}</p>
    </div>
</section>


<!-- Website Types Section -->
<section class="website-types">

    <!-- Corporate -->
    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <h2>{{ __('webdev.corporate_title') }}</h2>
                <p>{{ __('webdev.corporate_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('webdev.corporate_features') }}</h3>

                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_1') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_2') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_3') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_4') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_5') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.corporate_feat_6') }}</li>
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('webdev.corporate_price_label') }}</span>
                <h3>{{ __('webdev.corporate_price') }}</h3>
                <p>{{ __('webdev.corporate_price_note') }}</p>
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
                <h2>{{ __('webdev.ecom_title') }}</h2>
                <p>{{ __('webdev.ecom_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('webdev.ecom_features') }}</h3>

                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_1') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_2') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_3') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_4') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_5') }}</li>
                    <li><i class="fa-solid fa-circle-check"></i> {{ __('webdev.ecom_feat_6') }}</li>
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('webdev.ecom_price_label') }}</span>
                <h3>{{ __('webdev.ecom_price') }}</h3>
                <p>{{ __('webdev.ecom_price_note') }}</p>
            </div>
        </div>
    </div>

</section>


<!-- Call To Action Section -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>{{ __('webdev.cta_title') }}</h2>
                <h3>{{ __('webdev.cta_subtitle') }}</h3>
            </div>

            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('webdev.cta_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection
