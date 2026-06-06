<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panneau Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
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

            <div class="nav-group-title">Réalisations</div>

            <a href="{{ route('admin.achievements.create') }}" class="{{ request()->routeIs('admin.achievements.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Créer une réalisation
            </a>

            <a href="{{ route('admin.achievements.index') }}" class="{{ request()->routeIs('admin.achievements.index') || request()->routeIs('admin.achievements.edit') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                Nos réalisations
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

            <div class="nav-group-title">Ventes</div>

            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                Produits
            </a>

            <a href="{{ route('admin.products.create') }}" class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Ajouter un produit
            </a>

            <a href="{{ route('admin.requests.index') }}" class="{{ request()->routeIs('admin.requests.index') ? 'active' : '' }}">
                <i class="fas fa-inbox"></i>
                Toutes les demandes
            </a>

            <a href="{{ route('admin.requests.create') }}"
            class="{{ request()->routeIs('admin.requests.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Nouvelle demande
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
</html>

