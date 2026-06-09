@extends('layouts.app')

@section('title', __('home.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

<!-- Hero -->
<section class="hero" id="homeSection" data-section="home">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">
                {{ __('home.hero_title') }}
            </h1>

            <div class="hero-buttons">
                <a onclick="scrollToSection('contactSection')" class="btn btn-primary">
                    {{ __('home.hero_btn_contact') }}
                    <i class="fa-solid fa-paper-plane"></i>
                </a>

                <a onclick="scrollToSection('softwareSection')" class="btn btn-outline">
                    {{ __('header.software') }}
                    <i class="fa-solid fa-laptop-code"></i>
                </a>

                <a href="/store" class="btn btn-store-mobile">
                    {{ __('header.store') }}
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/over.png') }}" alt="{{ __('home.hero_title') }}">
        </div>

    </div>
</section>

<div class="divider"></div>

<!-- About  -->
<section class="about" id="aboutSection" data-section="about">
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
<section class="services"  id="servicesSection" data-section="services">
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
                    <div class="icon"><i class="fa-solid fa-chart-pie"></i></div>
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

<!-- Software  -->
<section class="software-section" id="softwareSection" data-section="software">
    <div class="software-container">

        <div class="software-content">

            <span class="software-label">
                <i class="fa-solid fa-layer-group"></i>
                {{ __('home.software_label') }}
            </span>

            <h2 class="software-title">
                {{ __('home.software_title') }}
            </h2>

            <p class="software-description">
                {{ __('home.software_description') }}
            </p>

            <div class="software-card">

                <div class="software-card-header">
                    <div class="software-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>

                    <div>
                        <h3>{{ __('home.wili_title') }}</h3>
                        <span>{{ __('home.wili_category') }}</span>
                    </div>
                </div>

                <p>
                    {{ __('home.wili_description') }}
                </p>

                <div class="software-features">
                    <span><i class="fa-solid fa-check"></i> {{ __('home.wili_feature_1') }}</span>
                    <span><i class="fa-solid fa-check"></i> {{ __('home.wili_feature_2') }}</span>
                    <span><i class="fa-solid fa-check"></i> {{ __('home.wili_feature_3') }}</span>
                </div>

                <a onclick="scrollToSection('contactSection')" class="software-link">
                    {{ __('home.software_cta') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>

            </div>

        </div>

        <div class="software-image">
            <img src="{{ asset('images/wili_pc.png') }}" alt="Wili Stock Management System">
        </div>

    </div>
</section>

<!-- Achievements  -->
<section class="acheivements" id="achievementsSection" data-section="achievements">
    <div class="acheivements-header">
        <h2 class="acheivements-title">{{ __('home.achievements_title') }}</h2>

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
                        @if($achievement->project_url)
                            <a class="view-link" href="{{ $achievement->project_url }}" target="_blank" rel="noopener">
                                {{ __('home.view_project') }}
                                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

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

<!-- Contact  -->
<section class="contact-section" id="contactSection" data-section="contact">
    <div class="contact-container">

        <!-- LEFT: CONTACT INFOS -->
        <div class="contact-info">

            <h2 class="contact-title">{{ __('contact.contact_title') }}</h2>

            <!-- Phone -->
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>{{ __('contact.phone_label') }}</h4>
                    <p>0561 65 51 00
                        <br> 0782 94 07 60
                        <br> 0540 29 62 00
                    </p>
                </div>
            </div>

            <!-- Email -->
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>{{ __('contact.email_label') }}</h4>
                    <p>contact@hss-agency.com</p>
                </div>
            </div>

            <!-- Facebook -->
            <div class="contact-item">
                <i class="fab fa-facebook-f"></i>
                <div>
                    <h4>{{ __('contact.facebook_label') }}</h4>
                    <p>facebook.com/HorizonSmartSystems</p>
                </div>
            </div>

            <!-- Address -->
            <div class="contact-item">
                <i class="fas fa-location-dot"></i>
                <div>
                    <h4>{{ __('contact.address_label') }}</h4>
                    <p>{{ __('contact.address_value') }}</p>
                </div>
            </div>

        </div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="contact-form">

            <h2>{{ __('contact.form_title') }}</h2>

            @if(session('success'))
                <div class="form-alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="form-alert danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label>{{ __('contact.form_fullname') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('contact.form_fullname_ph') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('contact.form_email_ph') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_phone') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ __('contact.form_phone_ph') }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('contact.form_subject') }}</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('contact.form_subject_ph') }}" required>
                    </div>

                </div>

                <div class="form-group message-group">
                    <label>{{ __('contact.form_message') }}</label>
                    <textarea name="message" placeholder="{{ __('contact.form_message_ph') }}" required>{{ old('message') }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="consent">
                    <label for="consent">
                        {{ __('contact.form_consent') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('contact.form_submit') }} <i class="fa-regular fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>
</section>

<!-- Map & Hours  -->
<section class="map-hours-section">

    <div class="map-hours-box">

        <!-- HOURS -->
        <div class="hours-part">
            <div class="hours-header">
                <i class="fas fa-clock"></i>
                <h3>{{ __('contact.hours_title') }}</h3>
            </div>

            <div class="hours-content">
                <div class="hour-row">
                    <span>{{ __('contact.saturday') }} - {{ __('contact.wednesday') }}</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row closed">
                    <span>{{ __('contact.thursday') }}</span>
                    <span>{{ __('contact.closed') }}</span>
                </div>

                <div class="hour-row closed">
                    <span>{{ __('contact.friday') }}</span>
                    <span>{{ __('contact.closed') }}</span>
                </div>
            </div>
        </div>

        <!-- MAP -->
        <div class="map-part">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3274.1601452089976!2d5.7204922742544975!3d34.85220317286611!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f509002f0dc033%3A0x427675b9fa5b3b87!2sHORIZON%20PC%20INFORMATIQUE!5e0!3m2!1sen!2sdz!4v1779480198528!5m2!1sen!2sdz"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

    </div>

</section>


<script>
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    const offset = 80; // space for fixed navbar

    const y = element.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
        top: y,
        behavior: 'smooth'
    });
}
</script>

@endsection


@section('script')
    <script type="module" src="{{ asset('js/home.js') }}"></script>
@endsection

