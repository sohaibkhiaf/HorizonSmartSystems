<footer class="site-footer">
    <div class="footer-container">

        {{-- Brand / Description --}}
        <div class="footer-section footer-brand">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Horizon Smart Systems Logo" class="footer-logo">

            <p class="footer-description">
                {{ __('footer.footer_desc') }}
            </p>

            <div class="footer-socials" aria-label="Social media">
                <span class="footer-social-icon">
                    <i class="fa-brands fa-facebook"></i>
                </span>

                <span class="footer-social-icon">
                    <i class="fa-brands fa-whatsapp"></i>
                </span>

            </div>
        </div>

        {{-- Quick Links --}}
        <div class="footer-section">
            <h3>{{ __('footer.quick_links') }}</h3>

            <ul class="footer-list">
                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('home_section')"
                        @else
                            href="/"
                        @endif
                        >
                        <i class="fa-solid fa-house"></i>
                        {{ __('footer.home') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/?section=services"
                        @endif
                        >
                        <i class="fa-solid fa-arrow-pointer"></i>
                        {{ __('footer.services') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('software_section')"
                        @else
                            href="/?section=software"
                        @endif
                        >
                        <i class="fa-brands fa-microsoft"></i>
                        {{ __('footer.software') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('achievements_section')"
                        @else
                            href="/?section=achievements"
                        @endif
                        >
                        <i class="fa-solid fa-medal"></i>
                        {{ __('footer.achievements') }}
                    </a>
                </li>

                <li>
                    <a href="/store" class="footer-link">
                        <i class="fa-solid fa-cart-shopping"></i>
                        {{ __('footer.store') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('contact_section')"
                        @else
                            href="/?section=contact"
                        @endif
                        >
                        <i class="fa-solid fa-message"></i>
                        {{ __('footer.contact') }}
                    </a>
                </li>
            </ul>
        </div>

        {{-- Services --}}
        <div class="footer-section">
            <h3>{{ __('footer.our_services') }}</h3>

            <ul class="footer-list">
                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/webdev"
                        @endif
                        >
                        <i class="fa-solid fa-globe"></i>
                        {{ __('footer.service_web') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/appdev"
                        @endif
                        >
                        <i class="fa-solid fa-laptop-code"></i>
                        {{ __('footer.service_apps') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/erp"
                        @endif
                        >
                        <i class="fa-solid fa-chart-pie"></i>
                        {{ __('footer.service_erp') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/systems"
                        @endif
                        >
                        <i class="fa-solid fa-gears"></i>
                        {{ __('footer.service_systems') }}
                    </a>
                </li>

                <li>
                    <a class="footer-link" 
                        @if ($page == 'APP')
                            onclick="scrollToSection('services_section')"
                        @else
                            href="/support"
                        @endif
                        >
                        <i class="fa-solid fa-headset"></i>
                        {{ __('footer.service_support') }}  
                    </a>
                </li>
            </ul>
        </div>

        {{-- Contact --}}
        <div class="footer-section footer-contact">
            <h3>{{ __('footer.contact_us') }}</h3>

            <ul class="footer-list">
                <li>
                    <i class="fa-solid fa-phone"></i>
                    <span dir="ltr">+213 561 65 51 00</span>
                </li>

                <li>
                    <i class="fa-solid fa-phone"></i>
                    <span dir="ltr">+213 782 94 07 60</span>
                </li>

                <li>
                    <i class="fa-solid fa-envelope"></i>
                    <span>horizonsmartsystems@gmail.com</span>
                </li>

                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ __('footer.location') }}</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 Horizon Smart Systems — {{ __('footer.rights') }}</p>
    </div>
</footer>

