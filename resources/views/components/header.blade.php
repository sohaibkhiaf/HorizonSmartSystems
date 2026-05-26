<header class="header" id="mainHeader">

    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Logo">
        </a>
    </div>

    <nav class="nav" id="navMenu">
        <a href="/" class="{{ $page === 'home' ? 'active' : '' }}">
            {{ __('header.home') }}
        </a>

        <a href="/about" class="{{ $page === 'about' ? 'active' : '' }}">
            {{ __('header.about') }}
        </a>

        <div class="nav-item has-dropdown">

            <a href="/services" class="{{ $page === 'services' ? 'active' : '' }}">
                {{ __('header.services') }} <span class="caret-down"><i class="fa-solid fa-caret-down"></i></span>
            </a>

            <!-- Mega Menu -->
            <div class="mega-menu">
                <div class="mega-column">
                    <h4>{{__('header.services_title')}}</h4>
                    <a class="service-link" href="/services/webdev"><i class="fa-solid fa-globe"></i> &nbsp; {{ __('header.service_web_title') }}</a>
                    <a class="service-link" href="/services/appdev"><i class="fa-solid fa-laptop-code"></i> &nbsp; {{ __('header.service_app_title') }}</a>
                    <a class="service-link" href="/services/erp"><i class="fa-solid fa-chart-pie"></i> &nbsp; {{__('header.service_erp_title')}}</a>
                    <a class="service-link" href="/services/systems"><i class="fa-solid fa-gears"></i> &nbsp; {{__('header.service_sys_title')}}</a>
                    <a class="service-link" href="/services/support"><i class="fa-solid fa-headset"></i> &nbsp; {{__('header.service_support_title')}}</a>
                </div>

                <div class="mega-column">
                    <h4>{{__('header.other_services_title')}}</h4>
                    <a class="service-link" href="/services"><i class="fa-solid fa-earth-europe"></i> &nbsp; {{ __('header.other_intl_title') }}</a>
                    <a class="service-link" href="/services"><i class="fa-solid fa-credit-card"></i> &nbsp; {{ __('header.other_subscriptions_title') }}</a>
                    <a class="service-link" href="/services"><i class="fa-solid fa-cloud-arrow-up"></i> &nbsp; {{ __('header.other_hosting_title') }}</a>
                    <a class="service-link" href="/services"><i class="fa-solid fa-circle-info"></i> &nbsp; {{ __('header.other_consulting_title') }}</a>
                </div>

            </div>
        </div>

        <a href="/achievements" class="{{ $page === 'achievements' ? 'active' : '' }}">
            {{ __('header.achievements') }}
        </a>

        <a href="/contact" class="{{ $page === 'contact' ? 'active' : '' }}">
            {{ __('header.contact') }}
        </a>
    </nav>

    <div class="actions">

        <div class="language-switcher">
            <a href="{{ route('language-switcher', 'fr') }}" class="{{ app()->getLocale() == 'fr' ? 'active' : '' }}">FR</a>
            <a href="{{ route('language-switcher', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ route('language-switcher', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">ع</a>
        </div>
    </div>

    <div class="menu-toggle" id="menuToggle">
        <i class="fa-solid fa-bars"></i>
    </div>

</header>

