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
                    <i class="fa-brands fa-linkedin"></i>
                </span>

                <span class="footer-social-icon">
                    <i class="fa-brands fa-square-instagram"></i>
                </span>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="footer-section">
            <h3>{{ __('footer.quick_links') }}</h3>

            <ul class="footer-list">
                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('homeSection')">
                        <i class="fa-solid fa-house"></i>
                        {{ __('footer.home') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-arrow-pointer"></i>
                        {{ __('footer.services') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('softwareSection')">
                        <i class="fa-brands fa-microsoft"></i>
                        {{ __('footer.software') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('achievementsSection')">
                        <i class="fa-solid fa-medal"></i>
                        {{ __('footer.achievements') }}
                    </button>
                </li>

                <li>
                    <a href="/store" class="footer-link">
                        <i class="fa-solid fa-cart-shopping"></i>
                        {{ __('footer.store') }}
                    </a>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('contactSection')">
                        <i class="fa-solid fa-message"></i>
                        {{ __('footer.contact') }}
                    </button>
                </li>
            </ul>
        </div>

        {{-- Services --}}
        <div class="footer-section">
            <h3>{{ __('footer.our_services') }}</h3>

            <ul class="footer-list">
                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-globe"></i>
                        {{ __('footer.service_web') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-laptop-code"></i>
                        {{ __('footer.service_apps') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-chart-pie"></i>
                        {{ __('footer.service_erp') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-gears"></i>
                        {{ __('footer.service_systems') }}
                    </button>
                </li>

                <li>
                    <button type="button" class="footer-link" onclick="scrollToSection('servicesSection')">
                        <i class="fa-solid fa-headset"></i>
                        {{ __('footer.service_support') }}
                    </button>
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
