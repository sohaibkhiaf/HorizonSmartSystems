@extends('layouts.app')

@section('title', __('home.page_title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')

<!-- Hero ---------------------------------------------------------------------- -->
<section class="hero" id="homeSection" data-section="home">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">
            <span class="hero-dev">{{ __('home.hero_development') }}</span> {{ __('home.hero_title') }}
            </h1>

            <div class="hero-buttons">
                <a onclick="scrollToSection('contact_section')" class="btn btn-primary">
                    {{ __('home.hero_btn_contact') }}
                    <i class="fa-solid fa-paper-plane"></i>
                </a>

                <a onclick="scrollToSection('softwareSection')" class="btn btn-outline">
                    {{ __('home.hero_btn_software') }}
                    <i class="fa-solid fa-laptop-code"></i>
                </a>

                <a href="/store" class="btn btn-store-mobile">
                    {{ __('home.hero_btn_store') }}
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/over.png') }}" alt="{{ __('home.hero_title') }}">
        </div>

    </div>
</section>

<!-- Services ---------------------------------------------------------------- -->
<section class="services" id="servicesSection" data-section="services">
    <div class="services-container">

        <div class="section-header">
            <h2 class="services-title">{{ __('home.services_title') }}</h2>
            <p class="section-description">
                {{ __('home.services_description') ?? '' }}
            </p>
        </div>

        <!-- Main Services -->
        <div class="services-group">

            <div class="swiper services-swiper">
                <div class="swiper-wrapper">

                    @foreach ($services as $service )
                        @if ($service->is_published)
                        <div class="swiper-slide service-card">
                            <div class="service-image">
                                <img src="{{ asset('uploads/'.$service->image ) }}" alt="{{ $service->name }}">
                            </div>
                            <h3>
                                @if (app()->getLocale() == 'ar')
                                    {{ $service->ar_name }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $service->fr_name }}
                                @else
                                    {{ $service->en_name }}
                                @endif
                            </h3>
                            <p>
                                @if (app()->getLocale() == 'ar')
                                    {{ $service->ar_short_description }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $service->fr_short_description }}
                                @else
                                    {{ $service->en_short_description }}
                                @endif
                            </p>
                            <a href="#" class="plus-link">
                                {{ __('home.learn_more') }}
                                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                            </a>
                        </div>   
                        @endif          
                    @endforeach

                </div>

                <!-- Navigation buttons -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <!-- Secondary Services -->
        <div class="other-services">
            <div class="other-service-card">
                <div class="other-service-icon">
                    <i class="fa-solid fa-earth-europe"></i>
                </div>
                <div>
                    <h3>{{ __('home.other_intl_title') }}</h3>
                    <p>{{ __('home.other_intl_desc') }}</p>
                </div>
            </div>

            <div class="other-service-card">
                <div class="other-service-icon">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <h3>{{ __('home.other_subscriptions_title') }}</h3>
                    <p>{{ __('home.other_subscriptions_desc') }}</p>
                </div>
            </div>

            <div class="other-service-card">
                <div class="other-service-icon">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div>
                    <h3>{{ __('home.other_hosting_title') }}</h3>
                    <p>{{ __('home.other_hosting_desc') }}</p>
                </div>
            </div>

            <div class="other-service-card">
                <div class="other-service-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div>
                    <h3>{{ __('home.other_consulting_title') }}</h3>
                    <p>{{ __('home.other_consulting_desc') }}</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Software ---------------------------------------------------------------- -->
<section class="software-section" id="softwareSection" data-section="software">
    <div class="software-container">

        <div class="software-content">
            <div class="software-header">
                <h2 class="software-title">
                    {{ __('home.software_title') }}
                </h2>

                <p class="software-description">
                    {{ __('home.software_description') }}
                </p>
            </div>

            @foreach ($software as $s )
            @if ($s->is_published)
            <div class="software-card">
                <div class="software-information">
                    <div class="software-card-header">
                        <div class="software-icon">
                            <img src="{{ asset('uploads/'.$s->icon) }}" alt="Wili Logo">
                        </div>

                        <div>
                            <h3>
                                @if (app()->getLocale() == 'ar')
                                    {{ $s->ar_name }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $s->fr_name }}
                                @else
                                    {{ $s->en_name }}
                                @endif
                            </h3>
                            <span>
                                @if (app()->getLocale() == 'ar')
                                    {{ $s->ar_short_description }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $s->fr_short_description }}
                                @else
                                    {{ $s->en_short_description }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <p>
                        @if (app()->getLocale() == 'ar')
                            {{ $s->ar_description }}
                        @elseif(app()->getLocale() == 'fr')
                            {{ $s->fr_description }}
                        @else
                            {{ $s->en_description }}
                        @endif
                    </p>

                    <a onclick="scrollToSection('contact_section')" class="software-link">
                        {{ __('home.software_cta') }}
                        <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                    </a>
                </div>

                <div class="software-image">
                    <img src="{{ asset('uploads/'.$s->image) }}" alt="Wili Stock Management System">
                </div>

                <a onclick="scrollToSection('contact_section')" class="software-link-mobile">
                    {{ __('home.software_cta') }}
                    <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
                </a>

            </div>
            @endif
            @endforeach

            

        </div>

    </div>
</section>

<!-- Achievements ----------------------------------------------------------- -->
<section class="achievements" id="achievementsSection" data-section="achievements">
    <div class="achievements-container">
        <div class="achievements-header">
            <h2 class="achievements-title">{{ __('home.achievements_title') }}</h2>
            <p class="achievements-description">
                {{ __('home.achievements_description') ?? '' }}
            </p>
        </div>

        <div class="swiper achievements-swiper">
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
                                <h3>
                                    @if (app()->getLocale() == 'ar')
                                        {{ $achievement->ar_title }}
                                    @elseif(app()->getLocale() == 'fr')
                                        {{ $achievement->fr_title }}
                                    @else
                                        {{ $achievement->en_title }}
                                    @endif
                                </h3>
                                <span class="type">
                                    @if (app()->getLocale() == 'ar')
                                        {{ $achievement->ar_type }}
                                    @elseif(app()->getLocale() == 'fr')
                                        {{ $achievement->fr_type }}
                                    @else
                                        {{ $achievement->en_type }}
                                    @endif
                                </span>
                            </div>
                            <p>
                                @if (app()->getLocale() == 'ar')
                                    {{ $achievement->ar_short_description  }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{$achievement->fr_short_description  }}
                                @else
                                    {{ $achievement->en_short_description  }}
                                @endif
                            </p>
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

            <!-- Navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- Contact ------------------------------------------------------------------- -->
<section class="contact-section" id="contact_section" data-section="contact">

    <div class="contact-heading">
        <h2>{{ __('home.contact_title') }}</h2>
        <p class="contact-description">
            {{ __('home.contact_description') ?? '' }}
        </p>
    </div>

    <div class="contact-container">

        <!-- LEFT: CONTACT INFOS -->
        <div class="contact-info">

            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>{{ __('home.phone_label') }}</h4>
                    <p>
                        0561 65 51 00
                        <br>0782 94 07 60
                        <br>0540 29 62 00
                    </p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>{{ __('home.email_label') }}</h4>
                    <p>contact@hss-agency.com</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fab fa-facebook-f"></i>
                <div>
                    <h4>{{ __('home.facebook_label') }}</h4>
                    <p>facebook.com/HorizonSmartSystems</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-location-dot"></i>
                <div>
                    <h4>{{ __('home.address_label') }}</h4>
                    <p>{{ __('home.address_value') }}</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <div>
                    <h4>{{ __('home.hours_title') }}</h4>

                    <div class="contact-hours">
                        <div class="hour-row">
                            <span>{{ __('home.saturday') }} - {{ __('home.wednesday') }}</span>
                            <span>08:30 - 16:30</span>
                        </div>

                        <div class="hour-row closed">
                            <span>{{ __('home.thursday') }}</span>
                            <span>{{ __('home.closed') }}</span>
                        </div>

                        <div class="hour-row closed">
                            <span>{{ __('home.friday') }}</span>
                            <span>{{ __('home.closed') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="contact-form">

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

            <form method="POST" action="{{ route('contact.message') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label>{{ __('home.form_fullname') }}</label>
                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name') }}"
                            placeholder="{{ __('home.form_fullname_ph') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_email') }}</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('home.form_email_ph') }}">
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_phone') }}</label>
                        <input
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="{{ __('home.form_phone_ph') }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label>{{ __('home.form_subject') }}</label>
                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            placeholder="{{ __('home.form_subject_ph') }}"
                            required>
                    </div>
                </div>

                <div class="form-group message-group">
                    <label>{{ __('home.form_message') }}</label>
                    <textarea
                        name="message"
                        placeholder="{{ __('home.form_message_ph') }}"
                        required>{{ old('message') }}</textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="consent">
                    <label for="consent">
                        {{ __('home.form_consent') }}
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('home.form_submit') }}
                    <i class="fa-regular fa-paper-plane"></i>
                </button>
            </form>

        </div>

        <!-- BOTTOM: MAP ONLY -->
        <div class="contact-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3274.1601452089976!2d5.7204922742544975!3d34.85220317286611!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f509002f0dc033%3A0x427675b9fa5b3b87!2sHORIZON%20PC%20INFORMATIQUE!5e0!3m2!1sen!2sdz!4v1779480198528!5m2!1sen!2sdz"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

    </div>
</section>


<!-- Floating Navigation Buttons -->
<div class="float-nav">
    <button id="btnScrollTop" class="float-btn is-hidden">
        <i class="fas fa-home"></i>
    </button>

    <button id="btnScrollNext" class="float-btn float-btn-down">
        <i class="fas fa-arrow-down"></i>
    </button>
</div>

<!-- Floating Social Buttons -->
<div class="social-float">
    
    <!-- Facebook -->
    <a href="https://www.facebook.com/HORIZON.INFORMATIQUE.BISKRA" target="_blank" class="social-btn fb">
        <i class="fab fa-facebook-f"></i>
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/+213659595372" target="_blank" class="social-btn wa">
        <i class="fab fa-whatsapp"></i>
    </a>

</div>

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

const swiper = new Swiper(".services-swiper", {
    slidesPerView: "auto",
    spaceBetween: 20,
    grabCursor: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

new Swiper(".achievements-swiper", {
    slidesPerView: "auto",
    spaceBetween: 20,
    grabCursor: true,
    centeredSlides: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});


const params = new URLSearchParams(window.location.search);
const section = params.get("section");

if (section) {
    const target = document.querySelector(`[data-section="${section}"]`);
    if (target) {
        target.scrollIntoView({ behavior: "smooth" });
    }
}

document.addEventListener("DOMContentLoaded", () => {

    const btnTop = document.getElementById("btnScrollTop");
    const btnNext = document.getElementById("btnScrollNext");
    const sections = document.querySelectorAll("[data-section]");
    const footer = document.querySelector("footer");

    const HEADER_OFFSET = 80;

    // ===== GET CURRENT SECTION INDEX =====
    function getCurrentSectionIndex() {
        let index = 0;

        sections.forEach((section, i) => {
            const rect = section.getBoundingClientRect();

            if (rect.top <= HEADER_OFFSET + 10) {
                index = i;
            }
        });

        return index;
    }

    // ===== SCROLL EVENTS =====
    window.addEventListener("scroll", () => {

        // Show / hide top button
        if (window.scrollY < window.innerHeight * 0.8) {
            btnTop.classList.add("is-hidden");
        } else {
            btnTop.classList.remove("is-hidden");
        }

        // Hide down button at footer
        if (footer) {
            const footerTop = footer.offsetTop;
            const scrollBottom = window.scrollY + window.innerHeight;

            if (scrollBottom >= footerTop) {
                btnNext.classList.add("is-hidden");
            } else {
                btnNext.classList.remove("is-hidden");
            }
        }
    });

    // ===== SCROLL TO TOP =====
    btnTop.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });

    // ===== SCROLL TO NEXT SECTION (FIXED) =====
    btnNext.addEventListener("click", () => {

        const currentIndex = getCurrentSectionIndex();
        const nextIndex = currentIndex + 1;

        if (sections[nextIndex]) {
            window.scrollTo({
                top: sections[nextIndex].offsetTop - HEADER_OFFSET ,
                behavior: "smooth"
            });
        } else {
            // No next section → go bottom
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: "smooth"
            });
        }

    });

});
</script>

@endsection

