@extends('layouts.app')

@section('title', __('about.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endsection

@section('content')

<!-- Hero Banner -->
<section class="hero-about">
    <div class="hero-overlay">
        <div class="hero-content">
            <h1>{{ __('about.banner_title') }}</h1>
            <p>{{ __('about.banner_subtitle') }}</p>
        </div>
    </div>
</section>

<!-- Hero  -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <p class="hero-small">{{ __('about.hero_label') }}</p>

            <h1 class="hero-title">
                {{ __('about.hero_title') }}
            </h1>
            <h3 class="hero-subtitle">
                {{ __('about.hero_subtitle') }}
            </h3>

            <p class="hero-description">
                {{ __('about.hero_description_1') }}
                <br><br>
                {{ __('about.hero_description_2') }}
            </p>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hss_agency.png') }}" alt="{{ __('about.hero_title') }}">
        </div>
    </div>
</section>


<!-- Values  -->
<section class="values">
    <div class="values-container">

        <!-- Notre vision Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="card-text">
                    <h3>{{ __('about.vision_title') }}</h3>
                    <p>{{ __('about.vision_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- Notre mission Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div class="card-text">
                    <h3>{{ __('about.mission_title') }}</h3>
                    <p>{{ __('about.mission_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- Nos valeurs Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div class="card-text">
                    <h3>{{ __('about.values_title') }}</h3>
                    <p>{!! nl2br(e(__('about.values_list'))) !!}</p>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- Team  -->
<section class="team">
    <div class="team-container">

        <div class="team-left">
            <h2>{{ __('about.team_title') }}</h2>
            <p>
                {{ __('about.team_description_1') }}
                <br><br>
                {{ __('about.team_description_2') }}
            </p>

            <a href="/contact" class="team-btn">
                {{ __('about.team_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
            </a>
        </div>

        <div class="team-right">

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Sohaib K</h3>
                <span>{{ __('about.member_sohaib_role') }}</span>
                <div class="socials">
                    <a href="https://www.facebook.com/sohaib.khiaf" target="_blank">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/sohaib-khiaf/" target="_blank">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Amine S</h3>
                <span>{{ __('about.member_amine_role') }}</span>
                <div class="socials">
                    <a href="#" >
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" >
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Said</h3>
                <span>{{ __('about.member_said_role') }}</span>
                <div class="socials">
                    <a href="#" >
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" >
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
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
                <h2>{{ __('about.cta_title') }}</h2>
                <h3>{{ __('about.cta_subtitle') }}</h3>
            </div>

            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    {{ __('about.cta_btn') }} <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>
            </div>

        </div>
    </div>
</section>

@endsection

