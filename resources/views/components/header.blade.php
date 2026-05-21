<header class="header" id="mainHeader">

    <!-- Left: Logo (image asset) -->
    <div class="logo">
        <a href="/">
            <img src="{{ asset('images/hss_logo.png') }}" alt="Logo">
        </a>
    </div>

    <!-- Center: Navigation -->
    <nav class="nav">
        <a href="/" class="{{ $active === 'accueil' ? 'active' : '' }}">Accueil</a>
        <a href="/about" class="{{ $active === 'apropos' ? 'active' : '' }}">À propos</a>
        <a href="/services" class="{{ $active === 'services' ? 'active' : '' }}">Services</a>
        <a href="/methods" class="{{ $active === 'methode' ? 'active' : '' }}">Méthode</a>
        <a href="/realisations" class="{{ $active === 'realisations' ? 'active' : '' }}">Réalisations</a>
        <a href="/contact" class="{{ $active === 'contact' ? 'active' : '' }}">Contact</a>
    </nav>

    <!-- Right: Button -->
    <div class="actions">
        <a href="/devis" class="btn">Demander un devis &nbsp; <i class="fa-solid fa-arrow-right"></i></a>
    </div>

</header>

