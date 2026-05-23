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

        <a href="/services" class="{{ $page === 'services' ? 'active' : '' }}">
            {{ __('header.services') }}
        </a>

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

