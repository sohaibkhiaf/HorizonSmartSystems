@extends('layouts.service')

@section('title')
    @if(app()->getLocale() == 'ar')
        {{ $service->ar_name }} — Horizon Smart Systems
    @elseif(app()->getLocale() == 'fr')
        {{ $service->fr_name }} — Horizon Smart Systems
    @else
        {{ $service->en_name }} — Horizon Smart Systems
    @endif
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
@endsection

@section('content')

@php
    $serviceName = app()->getLocale() == 'ar'
        ? $service->ar_name
        : (app()->getLocale() == 'fr'
            ? $service->fr_name
            : $service->en_name);

    $serviceShort = app()->getLocale() == 'ar'
        ? $service->ar_short_description
        : (app()->getLocale() == 'fr'
            ? $service->fr_short_description
            : $service->en_short_description);

    $serviceDescription = app()->getLocale() == 'ar'
        ? $service->ar_description
        : (app()->getLocale() == 'fr'
            ? $service->fr_description
            : $service->en_description);
@endphp

<section class="service-hero" id="service_section" data-section="service">
    <div class="service-hero-container">

        <div class="service-hero-content">

            <h1>{{ $serviceName }}</h1>

            <p>{{ $serviceShort }}</p>

            <div class="service-actions">
                <a onclick="scrollToSection('contact_section')" class="btn btn-primary">
                    {{ __('service.request_service') }}
                    <i class="fa-solid fa-paper-plane"></i>
                </a>

                <a href="/" class="btn btn-outline-light">
                    {{ __('service.back_home') }}
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>

        <div class="service-hero-image">
            @if($service->image)
                <img src="{{ asset('uploads/' . $service->image) }}" alt="{{ $serviceName }}">
            @else
                <div class="service-image-placeholder">
                    <i class="{{ $service->fa_icon ?? 'fa-solid fa-image' }}"></i>
                </div>
            @endif
        </div>

    </div>
</section>

<section class="service-details" id="service_details_section" data-section="service">
    <div class="service-details-container">

        <div class="service-main-card">
            <h2>{{ $serviceName }}</h2>

            <p>
                {!! nl2br(e($serviceDescription)) !!}
            </p>

            <a onclick="scrollToSection('contact_section')" class="service-cta">
                {{ __('service.contact_us') }}
                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
            </a>
        </div>

        <aside class="service-side-card">
            <h3>{{ __('service.other_services') }}</h3>

            @foreach($otherServices as $item)
                @php
                    $itemName = app()->getLocale() == 'ar'
                        ? $item->ar_name
                        : (app()->getLocale() == 'fr'
                            ? $item->fr_name
                            : $item->en_name);
                @endphp

                <a href="{{ route('services.show', $item) }}" class="side-service-link">
                    <i class="{{ $item->fa_icon ?? 'fa-solid fa-circle-info' }}"></i>
                    <span>{{ $itemName }}</span>
                </a>
            @endforeach
        </aside>

    </div>
</section>

<!-- Contact ------------------------------------------------------------------- -->
<section class="contact-section" id="contact_section" data-section="contact">

    <div class="contact-heading">
        <h2>{{ __('home.contact_title') }}</h2>

        <p class="contact-description">
            {{ __('home.contact_description') }}
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
                            <span>
                                {{ __('home.saturday') }}
                                -
                                {{ __('home.wednesday') }}
                            </span>

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
    <a href="https://wa.me/+213561655100" target="_blank" class="social-btn wa">
        <i class="fab fa-whatsapp"></i>
    </a>

</div>

<script>
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);

    if (!element) return;

    const offset = 80;
    const y = element.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
        top: y,
        behavior: 'smooth'
    });
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

