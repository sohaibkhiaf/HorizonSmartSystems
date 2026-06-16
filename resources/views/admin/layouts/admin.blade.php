<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panneau Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    
    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">

    @yield('style')
</head>
<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h2>HSS Admin</h2>
                <p>Gestionnaire du site</p>
            </div>
        </div>
        
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                Tableau de bord
            </a>

            <div class="nav-group-title">Utilisateurs</div>

            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Tous les utilisateurs
            </a>

            <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                Ajouter un utilisateur
            </a>

            <div class="nav-group-title">Messages</div>

            <a href="{{ route('admin.messages.waiting') }}" class="{{ request()->routeIs('admin.messages.waiting') ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                Messages en attente
            </a>

            <a href="{{ route('admin.messages.seen') }}" class="{{ request()->routeIs('admin.messages.seen') ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                Messages consultés
            </a>

            <div class="nav-group-title">Services</div>

            <a href="{{ route('admin.public_services.index') }}" class="{{ request()->routeIs('admin.public_services.index') || request()->routeIs('admin.public_services.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-bell-concierge"></i>
                Nos services
            </a>

            <a href="{{ route('admin.public_services.create') }}" class="{{ request()->routeIs('admin.public_services.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer un service
            </a>

            <div class="nav-group-title">Logiciels</div>

            <a href="{{ route('admin.public_software.index') }}" class="{{ request()->routeIs('admin.public_software.index') || request()->routeIs('admin.public_software.edit') ? 'active' : '' }}">
                <i class="fa-brands fa-accusoft"></i>
                Nos logiciels
            </a>

            <a href="{{ route('admin.public_software.create') }}" class="{{ request()->routeIs('admin.public_software.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer un logiciel
            </a>

            <div class="nav-group-title">Réalisations</div>

            <a href="{{ route('admin.achievements.index') }}" class="{{ request()->routeIs('admin.achievements.index') || request()->routeIs('admin.achievements.edit') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Nos réalisations
            </a>

            <a href="{{ route('admin.achievements.create') }}" class="{{ request()->routeIs('admin.achievements.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer une réalisation
            </a>

            <div class="nav-group-title">Store</div>

            <a href="{{ route('admin.store_subs.index') }}" class="{{ request()->routeIs('admin.store_subs.index') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i>
                Abonnements
            </a>

            <a href="{{ route('admin.store_subs.create') }}" class="{{ request()->routeIs('admin.store_subs.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Ajouter un abonnement
            </a>

            <a href="{{ route('admin.requests.index') }}" class="{{ request()->routeIs('admin.requests.index') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i>
                Toutes les demandes
            </a>

            <a href="{{ route('admin.requests.create') }}"
            class="{{ request()->routeIs('admin.requests.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Nouvelle demande
            </a>

            <div class="nav-group-title">Espace client</div>

            <a href="{{ route('admin.customer_portals.index') }}" class="{{ request()->routeIs('admin.customer_portals.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Portails clients
            </a>

            <div class="nav-group-title">Hero</div>

            <a href="{{ route('admin.hero.show', ['hero' => 1]) }}" class="{{ request()->routeIs('admin.hero.*') ? 'active' : '' }}">
                <i class="fa-solid fa-flag"></i>
                Modifier le hero
            </a>

        </nav>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">
                <i class="fas fa-right-from-bracket"></i>
                Déconnexion
            </button>
        </form>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1>@yield('page_title', 'Panneau Admin')</h1>
                <p>@yield('page_subtitle', 'Gérez le contenu de votre site')</p>
            </div>
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

</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const activeLink = document.querySelector('.admin-nav a.active');

        if (activeLink) {
            activeLink.scrollIntoView({
                behavior: "smooth",   // or "auto" if you prefer no animation
                block: "center"       // "start" | "center" | "end"
            });
        }
    });
</script>

</html>
