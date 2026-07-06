<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panneau Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/hss_logo_icon.png') }}"/>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    
    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}?v={{ config('app.asset_version') }}">

    @yield('style')
</head>
<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar" id="adminSidebar">

    <div class="admin-logo">
        <div class="admin-logo-icon">
            <i class="fas fa-layer-group"></i>
        </div>

        <div class="admin-logo-text">
            <h2>HSS Admin</h2>
            <p>Gestionnaire du site</p>
            <p>{{ auth()->user()->name }}</p>
        </div>

        <button type="button" class="sidebar-close-btn" id="sidebarClose" aria-label="Fermer le menu">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
        
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                Tableau de bord
            </a>

            <div class="nav-group-title">Chat box</div>

            <a href="{{ route('admin.chats.index') }}" class="{{ request()->routeIs('admin.chats.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i>
                Messagerie
            </a>

            <div class="nav-group-title">Utilisateurs</div>

            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.edit') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Tous les utilisateurs
            </a>

            <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                Ajouter un utilisateur
            </a>

            <div class="nav-group-title">Store</div>

            <a href="{{ route('admin.store_requests.index') }}" class="{{ request()->routeIs('admin.store_requests.index') || request()->routeIs('admin.store_requests.show') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i>
                Toutes les demandes
            </a>

            <a href="{{ route('admin.store_requests.create') }}" class="{{ request()->routeIs('admin.store_requests.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Nouvelle demande
            </a>

            <a href="{{ route('admin.store_subs.index') }}" class="{{ request()->routeIs('admin.store_subs.index') || request()->routeIs('admin.store_subs.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i>
                Produits
            </a>

            <a href="{{ route('admin.store_subs.create') }}" class="{{ request()->routeIs('admin.store_subs.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Ajouter un produit
            </a>

            <div class="nav-group-title">Messages</div>

            <a href="{{ route('admin.public_messages.waiting') }}" class="{{ request()->routeIs('admin.public_messages.waiting') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                Messages en attente
            </a>

            <a href="{{ route('admin.public_messages.seen') }}" class="{{ request()->routeIs('admin.public_messages.seen') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                Messages consultés
            </a>

            <div class="nav-group-title">Contenu du site</div>

            <a href="{{ route('admin.public_hero.show', ['hero' => 1]) }}" class="{{ request()->routeIs('admin.public_hero.*') ? 'active' : '' }}">
                <i class="fa-solid fa-flag"></i>
                Hero
            </a>

            <a href="{{ route('admin.public_services.index') }}" class="{{ request()->routeIs('admin.public_services.index') || request()->routeIs('admin.public_services.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-bell-concierge"></i>
                Services
            </a>

            <a href="{{ route('admin.public_services.create') }}" class="{{ request()->routeIs('admin.public_services.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer un service
            </a>

            <a href="{{ route('admin.public_software.index') }}" class="{{ request()->routeIs('admin.public_software.index') || request()->routeIs('admin.public_software.edit') ? 'active' : '' }}">
                <i class="fa-brands fa-accusoft"></i>
                Logiciels
            </a>

            <a href="{{ route('admin.public_software.create') }}" class="{{ request()->routeIs('admin.public_software.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer un logiciel
            </a>

            <a href="{{ route('admin.software_demos.index') }}" class="{{ request()->routeIs('admin.software_demos.index') || request()->routeIs('admin.software_demos.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-display"></i>
                Démos logiciels
            </a>

            <a href="{{ route('admin.software_demos.create') }}" class="{{ request()->routeIs('admin.software_demos.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer une démo
            </a>

            <a href="{{ route('admin.achievements.index') }}" class="{{ request()->routeIs('admin.achievements.index') || request()->routeIs('admin.achievements.edit') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Réalisations
            </a>

            <a href="{{ route('admin.achievements.create') }}" class="{{ request()->routeIs('admin.achievements.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer une réalisation
            </a>

            @if(auth()->user()->isAdmin())
                <div class="nav-group-title">Logs</div>

                <a href="{{ route('admin.logs.index') }}" class="{{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Journal d'activité
                </a>
            @endif

        </nav>
        
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">
                <i class="fas fa-right-from-bracket"></i>
                Déconnexion
            </button>
        </form>
    </aside>

    <div class="admin-sidebar-overlay" id="sidebarOverlay"></div>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>@yield('page_title', 'Panneau Admin')</h1>
                <p>@yield('page_subtitle', 'Gérez le contenu de votre site')</p>
            </div>

            <button type="button"
                    class="mobile-sidebar-toggle"
                    id="sidebarToggle"
                    aria-label="Ouvrir le menu"
                    aria-controls="adminSidebar"
                    aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </header>

        @if(session('success'))
            <div class="alert success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const activeLink = document.querySelector('.admin-nav a.active');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const navLinks = document.querySelectorAll('.admin-nav a');

        const isMobile = () => window.matchMedia('(max-width: 800px)').matches;

        function openSidebar() {
            document.body.classList.add('sidebar-open');

            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', 'true');
            }

            if (activeLink) {
                setTimeout(() => {
                    activeLink.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }, 250);
            }
        }

        function closeSidebar() {
            document.body.classList.remove('sidebar-open');

            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', 'false');
            }
        }

        if (activeLink && !isMobile()) {
            activeLink.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (isMobile()) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                closeSidebar();
            }
        });
    });
</script>

@yield('script')

</body>
</html>
