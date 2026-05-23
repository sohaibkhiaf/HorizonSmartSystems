<footer class="site-footer">
    <div class="footer-container">

        <div class="footer-section">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Logo" class="footer-logo">

            <p class="footer-description">
                {{ __('footer.footer_desc') }}
            </p>

            <div class="footer-socials">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h3>{{ __('footer.quick_links') }}</h3>
            <ul>
                <li><a href="/"><i class="fa-solid fa-house"></i>&nbsp;{{ __('footer.home') }}</a></li>
                <li><a href="/about"><i class="fa-solid fa-circle-question"></i>&nbsp;{{ __('footer.about') }}</a></li>
                <li><a href="/services"><i class="fa-solid fa-arrow-pointer"></i>&nbsp;{{ __('footer.services') }}</a></li>
                <li><a href="/achievements"><i class="fa-solid fa-medal"></i>&nbsp;{{ __('footer.achievements') }}</a></li>
                <li><a href="/contact"><i class="fa-solid fa-message"></i>&nbsp;{{ __('footer.contact') }}</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>{{ __('footer.our_services') }}</h3>
            <ul>
                <li><i class="fa-solid fa-globe"></i>&nbsp;{{ __('footer.service_web') }}</li>
                <li><i class="fa-solid fa-code"></i>&nbsp;{{ __('footer.service_apps') }}</li>
                <li><i class="fa-solid fa-gears"></i>&nbsp;{{ __('footer.service_systems') }}</li>
                <li><i class="fa-solid fa-desktop"></i>&nbsp;{{ __('footer.service_support') }}</li>
            </ul>
        </div>

        <div class="footer-section footer-contact">
            <h3>{{ __('footer.contact_us') }}</h3>
            <ul>
                <li><i class="fa-solid fa-phone"></i><span dir="ltr">+213 561 65 51 00</span></li>
                <li><i class="fa-solid fa-phone"></i><span dir="ltr">+213 782 94 07 60</span></li>
                <li><i class="fa-solid fa-envelope"></i> horizonsmartsystems@gmail.com</li>
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <a href="https://maps.app.goo.gl/KbLsAEh2LawQRG129" target="_blank">
                        {{ __('footer.location') }}
                    </a>
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 Horizon Smart Systems — {{ __('footer.rights') }}</p>
    </div>
</footer>

