@php
    $locale = in_array(app()->getLocale(), ['ar', 'fr', 'en'])
        ? app()->getLocale()
        : 'fr';

    $nameField = $locale . '_name';
    $shortDescriptionField = $locale . '_short_description';
    $descriptionField = $locale . '_description';

    $softwareName = $software->{$nameField};
    $softwareShort = $software->{$shortDescriptionField};
    $softwareDescription = $software->{$descriptionField};
@endphp

@extends('layouts.software')

@section('title', $softwareName . ' — ' . __('software.title_suffix'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/software.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

<section class="software-hero" id="software_section" data-section="software">
    <div class="software-hero-container">

        <div class="software-hero-content">

            <h1>{{ $softwareName }}</h1>

            <p>{{ $softwareShort }}</p>

            <div class="software-actions">
                <a href="#contact_section" onclick="scrollToSection('contact_section'); return false;" class="btn btn-primary">
                    {{ __('software.request') }}
                    <i class="fa-solid fa-paper-plane"></i>
                </a>

                <a href="{{ url('/') }}" class="btn btn-outline-dark">
                    {{ __('software.back_home') }}
                    <i class="fa-solid fa-house"></i>
                </a>
            </div>
        </div>

        <div class="software-hero-image">
            @if($software->image)
                <img src="{{ asset('uploads/' . $software->image) }}" alt="{{ $softwareName }}">
            @else
                <div class="software-image-placeholder">
                    <i class="{{ $software->icon ?? 'fa-solid fa-laptop-code' }}"></i>
                </div>
            @endif
        </div>

    </div>
</section>

<section class="software-details" id="software_details_section" data-section="software-details">
    <div class="software-details-container">

        <div class="software-main-card">
            <h2>{{ $softwareName }}</h2>

            <p>
                {!! nl2br(e($softwareDescription)) !!}
            </p>

            @php
                $softwareDemos = $software->demos->where('is_active', true);
            @endphp

            @if($softwareDemos->count())
                <div class="software-demo-section" id="software_demos_section" >
                    <div class="software-demo-container">

                        <div class="software-demo-slider-card">
                            <div class="software-demo-slider-shell">

                                <button type="button" class="software-demo-nav software-demo-prev" aria-label="{{ __('customer_portal.previous') }}">
                                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                                </button>

                                <div class="swiper software-demo-swiper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                    <div class="swiper-wrapper">

                                        @foreach($softwareDemos as $demo)
                                            <div class="swiper-slide">
                                                <a href="#contact_section"
                                                onclick="scrollToSection('contact_section'); return false;"
                                                class="software-demo-card">

                                                    <div class="software-demo-image">
                                                        @if($demo->image)
                                                            <img
                                                                src="{{ asset('uploads/' . $demo->image) }}"
                                                                alt="{{ $demo->name }}"
                                                                loading="lazy">
                                                        @else
                                                            <div class="software-demo-placeholder">
                                                                <i class="fa-solid fa-display"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="software-demo-content">
                                                        <h3>{{ $demo->name }}</h3>

                                                        <span class="software-demo-link">
                                                            {{ __('software.software_cta') }}
                                                            <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                                                        </span>
                                                    </div>

                                                </a>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                                <button type="button" class="software-demo-nav software-demo-next" aria-label="{{ __('customer_portal.next') }}">
                                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                                </button>

                            </div>
                        </div>

                    </div>
            </div>
            @endif

            <a href="#contact_section" onclick="scrollToSection('contact_section'); return false;" class="software-cta">
                {{ __('software.contact') }}
                <i class="{{ app()->getLocale() == 'ar' ? 'fas fa-arrow-left' : 'fas fa-arrow-right' }}"></i>
            </a>
        </div>

        <aside class="software-side-card">
            <h3>{{ __('software.other_software') }}</h3>

            @forelse($otherSoftware as $item)
                @php
                    $itemName = $item->{$nameField};
                @endphp

                <a href="{{ route('software.show', $item) }}" class="side-software-link">

                    @if($item->icon)
                        <img
                            src="{{ asset('uploads/' . $item->icon) }}"
                            alt="{{ $itemName }}"
                            class="side-software-icon">
                    @else
                        <span class="side-software-placeholder">
                            <i class="fa-solid fa-laptop-code"></i>
                        </span>
                    @endif

                    <span>{{ $itemName }}</span>
                </a>
            @empty
                <p class="empty-text">
                    {{ __('software.empty_other_software') }}
                </p>
            @endforelse
        </aside>

    </div>
</section>

<section class="contact-section" id="contact_section" data-section="contact">

    <div class="contact-heading">
        <h2>{{ __('home.contact_title') }}</h2>

        <p class="contact-description">
            {{ __('home.contact_description') }}
        </p>
    </div>

    <div class="contact-container">

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
                    <p>horizonsmartsystems@gmail.com</p>
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
                            value="{{ old('subject', $softwareName) }}"
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
                    <input type="checkbox" id="consent" checked>

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

<div class="float-nav">
    <button id="btnScrollTop" class="float-btn is-hidden">
        <i class="fas fa-arrow-up"></i>
    </button>

    <button id="btnScrollNext" class="float-btn float-btn-down">
        <i class="fas fa-arrow-down"></i>
    </button>
</div>

<div class="social-float">

    @if ($hero->facebook_url)
        <a href="{{ $hero->facebook_url }}" target="_blank" class="social-btn fb">
            <i class="fab fa-facebook-f"></i>
        </a>
    @endif

    @if ($hero->instagram_url)
        <a href="{{ $hero->instagram_url }}" target="_blank" class="social-btn ig">
            <i class="fa-brands fa-square-instagram"></i>
        </a>
    @endif

    @if ($hero->whatsapp_url)
        <a href="{{ $hero->whatsapp_url }}" target="_blank" class="social-btn wa">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

    @if ($hero->linked_in_url)
        <a href="{{ $hero->linked_in_url }}" target="_blank" class="social-btn li">
            <i class="fa-brands fa-linkedin"></i>
        </a>
    @endif

</div>

<script>
    const HEADER_OFFSET = 40;

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);

        if (!element) return;

        window.scrollTo({
            top: element.offsetTop - HEADER_OFFSET,
            behavior: "smooth"
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const btnTop = document.getElementById("btnScrollTop");
        const btnNext = document.getElementById("btnScrollNext");
        const sections = Array.from(document.querySelectorAll("[data-section]"));
        const footer = document.querySelector("footer");

        if (!btnTop || !btnNext || sections.length === 0) return;

        function getCurrentSectionIndex() {
            let currentIndex = 0;

            sections.forEach((section, index) => {
                if (window.scrollY >= section.offsetTop - HEADER_OFFSET - 20) {
                    currentIndex = index;
                }
            });

            return currentIndex;
        }

        function isFooterVisible() {
            if (!footer) return false;

            return window.scrollY + window.innerHeight >= footer.offsetTop - 20;
        }

        function updateFloatingButtons() {
            const currentIndex = getCurrentSectionIndex();

            if (currentIndex > 0 || window.scrollY > 120) {
                btnTop.classList.remove("is-hidden");
            } else {
                btnTop.classList.add("is-hidden");
            }

            if (isFooterVisible()) {
                btnNext.classList.add("is-hidden");
            } else {
                btnNext.classList.remove("is-hidden");
            }
        }

        btnTop.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        btnNext.addEventListener("click", () => {
            const currentIndex = getCurrentSectionIndex();
            const nextSection = sections[currentIndex + 1];

            if (nextSection) {
                window.scrollTo({
                    top: nextSection.offsetTop - HEADER_OFFSET,
                    behavior: "smooth"
                });
            } else {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: "smooth"
                });
            }
        });

        window.addEventListener("scroll", updateFloatingButtons);
        window.addEventListener("resize", updateFloatingButtons);

        updateFloatingButtons();
    });
</script>

<script>
function initSoftwareDemoSwiper() {
    if (typeof Swiper === "undefined") return;

    document.querySelectorAll(".software-demo-swiper").forEach((swiperElement) => {
        if (swiperElement.swiper) return;

        const shell = swiperElement.closest(".software-demo-slider-shell");
        if (!shell) return;

        new Swiper(swiperElement, {
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            simulateTouch: true,
            allowTouchMove: true,
            threshold: 6,
            centeredSlides: true,
            watchOverflow: true,

            preventClicks: true,
            preventClicksPropagation: true,

            navigation: {
                nextEl: shell.querySelector(".software-demo-next"),
                prevEl: shell.querySelector(".software-demo-prev"),
            },

            breakpoints: {
                560: {
                    slidesPerView: 2,
                    spaceBetween: 14,
                    centeredSlides: false,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 16,
                    centeredSlides: false,
                },
                1100: {
                    slidesPerView: 4,
                    spaceBetween: 18,
                    centeredSlides: false,
                },
                1300: {
                    slidesPerView: 4,
                    spaceBetween: 18,
                    centeredSlides: false,
                },
            }
        });
    });
}
document.addEventListener("DOMContentLoaded", initSoftwareDemoSwiper);
</script>

@endsection

