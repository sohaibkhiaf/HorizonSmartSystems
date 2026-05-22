<header class="header" id="mainHeader">

    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/hss_logo_1.png') }}" alt="Logo">
        </a>
    </div>

    <nav class="nav"  id="navMenu">
        <a href="/" class="{{ $page === 'home' ? 'active' : '' }}">Accueil</a>
        <a href="/about" class="{{ $page === 'about' ? 'active' : '' }}">À propos</a>
        <a href="/services" class="{{ $page === 'services' ? 'active' : '' }}">Services</a>
        <a href="/achievements" class="{{ $page === 'achievements' ? 'active' : '' }}">Réalisations</a>
        <a href="/contact" class="{{ $page === 'contact' ? 'active' : '' }}">Contact</a>
    </nav>

    <div class="menu-toggle" id="menuToggle">
        <i class="fa-solid fa-bars"></i>
    </div>

    <div class="actions">
        <a href="/contact" class="btn">Contactez-nous &nbsp; <i class="fa-solid fa-arrow-right"></i></a>
    </div>

</header>

