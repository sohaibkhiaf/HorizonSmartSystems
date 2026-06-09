<header class="header" id="mainHeader">

    {{-- Logo --}}
    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Horizon Smart Systems Logo">
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="nav" id="navMenu">

        <a class="nav-tab {{ $page === 'home' ? 'active' : '' }}"
           onclick="scrollToSection('homeSection')"
           data-target="home">
            {{ __('header.home') }}
        </a>

        {{-- Services --}}
        <div class="nav-item has-dropdown">

            <a class="nav-tab {{ $page === 'services' ? 'active' : '' }}"
               onclick="scrollToSection('servicesSection')"
               data-target="services">
                {{ __('header.services') }}
                <span class="caret-down">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>

            <div class="mega-menu">
                <div class="mega-column">
                    <h4>{{ __('header.services_title') }}</h4>

                    <a class="service-link" href="/services/webdev">
                        <i class="fa-solid fa-globe"></i>
                        <span>{{ __('header.service_web_title') }}</span>
                    </a>

                    <a class="service-link" href="/services/appdev">
                        <i class="fa-solid fa-laptop-code"></i>
                        <span>{{ __('header.service_app_title') }}</span>
                    </a>

                    <a class="service-link" href="/services/erp">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>{{ __('header.service_erp_title') }}</span>
                    </a>

                    <a class="service-link" href="/services/systems">
                        <i class="fa-solid fa-gears"></i>
                        <span>{{ __('header.service_sys_title') }}</span>
                    </a>

                    <a class="service-link" href="/services/support">
                        <i class="fa-solid fa-headset"></i>
                        <span>{{ __('header.service_support_title') }}</span>
                    </a>
                </div>

                <div class="mega-column">
                    <h4>{{ __('header.other_services_title') }}</h4>

                    <a class="service-link" href="/services">
                        <i class="fa-solid fa-earth-europe"></i>
                        <span>{{ __('header.other_intl_title') }}</span>
                    </a>

                    <a class="service-link" href="/services/subs">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>{{ __('header.other_subscriptions_title') }}</span>
                    </a>

                    <a class="service-link" href="/services">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>{{ __('header.other_hosting_title') }}</span>
                    </a>

                    <a class="service-link" href="/services">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ __('header.other_consulting_title') }}</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Software --}}
        <div class="nav-item has-dropdown">

            <a class="nav-tab {{ $page === 'software' ? 'active' : '' }}"
               onclick="scrollToSection('softwareSection')"
               data-target="software">
                {{ __('header.software') }}
                <span class="caret-down">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>

            <div class="mega-menu mega-menu-single">
                <div class="mega-column">
                    <h4>{{ __('header.software_title') }}</h4>

                    <a class="service-link" onclick="scrollToSection('softwareSection')">
                        <i class="fa-solid fa-w"></i>
                        <span>{{ __('header.software_wili') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <a class="nav-tab {{ $page === 'achievements' ? 'active' : '' }}"
           onclick="scrollToSection('achievementsSection')"
           data-target="achievements">
            {{ __('header.achievements') }}
        </a>

        <a class="nav-tab {{ $page === 'contact' ? 'active' : '' }}"
           onclick="scrollToSection('contactSection')"
           data-target="contact">
            {{ __('header.contact') }}
        </a>

    </nav>

    {{-- Store Button --}}
    <a href="/store" class="store-btn store-btn-primary">
        <i class="fa-solid fa-cart-shopping"></i>
        {{ __('header.store') }}
    </a>

    {{-- Language Switcher --}}
    <div class="actions">
        <div class="language-switcher">
            <a href="{{ route('language-switcher', 'fr') }}"
               class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">
                FR
            </a>

            <a href="{{ route('language-switcher', 'en') }}"
               class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">
                EN
            </a>

            <a href="{{ route('language-switcher', 'ar') }}"
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

    const header = document.getElementById("mainHeader");
    const menuToggle = document.getElementById("menuToggle");
    const navMenu = document.getElementById("navMenu");
    const sections = document.querySelectorAll("[data-section]");
    const tabs = document.querySelectorAll(".nav-tab");

    window.scrollToSection = function(sectionId) {
        const element = document.getElementById(sectionId);

        if (!element) return;

        const offset = 80;
        const y = element.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
            top: y,
            behavior: "smooth"
        });

        navMenu.classList.remove("active");
    };

    function handleHeaderScroll() {
        if (!header) return;

        if (window.scrollY > 50) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    }

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

    window.addEventListener("scroll", () => {
        handleHeaderScroll();
        updateActiveTab();
    });

    if (menuToggle && navMenu) {
        menuToggle.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });
    }

});
</script>

