<header class="header" id="main_header">

    {{-- Logo --}}
    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Horizon Smart Systems Logo">
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="nav" id="navMenu">

        <a class="nav-tab"
            @if ($page == 'APP')
                onclick="scrollToSection('home_section')"
            @else
                href="/"
            @endif
           data-target="home">
            {{ __('header.home') }}
        </a>

        {{-- Services --}}
        <div class="nav-item has-dropdown">

            <a class="nav-tab {{ $page == "SERVICE" ? 'active' : '' }}"
                @if ($page == 'APP')
                    onclick="scrollToSection('services_section')"
                @else
                    href="/?section=services"
                @endif
               data-target="services">
                {{ __('header.services') }}
                <span class="caret-down">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>

            <div class="mega-menu">
                <div class="mega-column">
                    <h4>{{ __('header.services_title') }}</h4>

                    @foreach($services as $service)
                        @if ($service->is_published && $service->type == "main")
                        <a class="service-link" href="{{ route('services.show', $service) }}">
                            <i class="{{ $service->fa_icon ?? 'fa-solid fa-circle-info' }}"></i>
                            <span>
                                @if(app()->getLocale() == 'ar')
                                    {{ $service->ar_name }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $service->fr_name }}
                                @else
                                    {{ $service->en_name }}
                                @endif
                            </span>
                        </a>
                        @endif
                    @endforeach
                </div>

                <div class="mega-column">
                    <h4>{{ __('header.other_services_title') }}</h4>

                    @foreach($services as $service)
                        @if ($service->is_published && $service->type == "other")
                        <a class="service-link" href="{{ route('services.show', $service) }}">
                            <i class="{{ $service->fa_icon ?? 'fa-solid fa-circle-info' }}"></i>
                            <span>
                                @if(app()->getLocale() == 'ar')
                                    {{ $service->ar_name }}
                                @elseif(app()->getLocale() == 'fr')
                                    {{ $service->fr_name }}
                                @else
                                    {{ $service->en_name }}
                                @endif
                            </span>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Software --}}
        <a class="nav-tab"
            @if ($page == 'APP')
                onclick="scrollToSection('software_section')"
            @else
                href="/?section=software"
            @endif
            data-target="software">
            {{ __('header.software') }}
        </a>

        <a class="nav-tab"
            @if ($page == 'APP')
                onclick="scrollToSection('achievements_section')"
            @else
                href="/?section=achievements"
            @endif
            data-target="achievements">
            {{ __('header.achievements') }}
        </a>

        <a class="nav-tab"
            @if ($page == 'APP' || $page == 'SERVICE')
                onclick="scrollToSection('contact_section')"
            @else
                href="/?section=contact"
            @endif
            data-target="contact">
            {{ __('header.contact') }}
        </a>

        <a class="nav-tab {{ $page == "AUTH" || $page == "CUSTOMER" ? 'active' : '' }}" 
            href="/login">
            {{ __('header.customer_portal') }}
        </a>

        @if ($page == "STORE")
        <a class="nav-tab active" href="/store">
            <i class="fa-solid fa-cart-shopping"></i>
            {{ __('header.store') }}
        </a>
        @endif

    </nav>

    {{-- Store Button --}}
    @if ($page != "STORE")
    <a href="/store" class="store-btn store-btn-primary">
        <i class="fa-solid fa-cart-shopping"></i>
        {{ __('header.our_store') }}
    </a>
    @endif

    {{-- Language Switcher --}}
    <div class="actions">
        <div class="language-switcher">
            <a href="{{ route('language.switcher', 'fr') }}"
               class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">
                FR
            </a>

            <a href="{{ route('language.switcher', 'en') }}"
               class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">
                EN
            </a>

            <a href="{{ route('language.switcher', 'ar') }}"
               class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                ع
            </a>
        </div>
    </div>

    {{-- Mobile Menu Button --}}
    <button class="menu-toggle" id="menuToggle" type="button" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
    </button>

</header>

<script>
document.addEventListener("DOMContentLoaded", () => {

    /* ==========================================================================
       Elements
    ========================================================================== */

    const header = document.getElementById("main_header");
    const menuToggle = document.getElementById("menuToggle");
    const navMenu = document.getElementById("navMenu");

    const sections = document.querySelectorAll("[data-section]");
    const tabs = document.querySelectorAll(".nav-tab");

    /* ==========================================================================
       Navigation Active Tab
    ========================================================================== */

    function updateActiveTab() {

        let current = "";

        sections.forEach(section => {

            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;

            if (window.scrollY >= sectionTop - sectionHeight / 3) {
                current = section.getAttribute("data-section");
            }
        });

        tabs.forEach(tab => {

            tab.classList.remove("active");

            if (tab.getAttribute("data-target") === current) {
                tab.classList.add("active");
            }
        });
    }

    /* ==========================================================================
       Mobile Menu
    ========================================================================== */

    if (menuToggle && navMenu) {

        menuToggle.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });

    }

    /* ==========================================================================
       Scroll Events
    ========================================================================== */

    window.addEventListener("scroll", () => {

        if (
            @json($page != 'STORE') &&
            @json($page != 'AUTH') &&
            @json($page != 'SERVICE') &&
            @json($page != 'CUSTOMER')
        ) {
            updateActiveTab();
        }

    });

    /* ==========================================================================
       Initial State
    ========================================================================== */

    if (@json($page == "APP")) {
        tabs[0].classList.add("active");
    }

});
</script>

