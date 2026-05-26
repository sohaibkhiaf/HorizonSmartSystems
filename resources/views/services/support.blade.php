@extends('layouts.app')

@section('title', __('support.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
@endsection

@section('content')

<!-- Hero -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">{{ __('support.hero_title') }}</h1>
            <p class="hero-description">{{ __('support.hero_description') }}</p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">
                    {{ __('support.hero_btn_contact') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/support.png') }}" alt="{{ __('support.hero_title') }}">
        </div>

    </div>
</section>

<!-- Description -->
<section class="description">
    <div class="description-container">
        <h2>{{ __('support.desc_title') }}</h2>
        <p>{{ __('support.desc_text') }}</p>
    </div>
</section>


<!-- Types  -->
<section class="service-types">

    <!-- Corporate -->
    <div class="service-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-wrench"></i>
            </div>

            <div>
                <h2>{{ __('support.support_title') }}</h2>
                <p>{{ __('support.support_subtitle') }}</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>{{ __('support.core_features') }}</h3>

                <ul>
                    @foreach(__('support.support_features') as $feature)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="price-box">
                <span>{{ __('support.price_label') }}</span>
                <h3>{{ __('support.support_price') }}</h3>
                <p>{{ __('support.price_note') }}</p>
            </div>
        </div>
    </div>

</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>{{ __('support.cta_title') }}</h2>
                <h3>{{ __('support.cta_subtitle') }}</h3>
            </div>

            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('support.cta_btn') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>

    </div>
</section>

@endsection



