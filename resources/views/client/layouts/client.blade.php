<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Espace client')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/hss_logo_icon.png') }}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">

    <style>
        .admin-logo-icon{background:#ecfdf5;color:#047857}
        .admin-nav a:hover,.admin-nav a.active,.logout-form button:hover{background:#ecfdf5;color:#047857}
        .client-kicker{display:inline-flex;align-items:center;gap:8px;color:#047857;font-size:12px;font-weight:700;text-transform:uppercase;margin-bottom:6px}
    </style>
    @yield('style')
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <h2>HSS Client</h2>
                <p>Espace client</p>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                Tableau de bord
            </a>

            <div class="nav-group-title">Achats</div>
            <a href="{{ route('client.orders.index') }}" class="{{ request()->routeIs('client.orders.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i>
                Mes commandes
            </a>

            <div class="nav-group-title">Services</div>
            <a href="{{ route('client.services.index') }}" class="{{ request()->routeIs('client.services.*') ? 'active' : '' }}">
                <i class="fas fa-cubes"></i>
                Mes services
            </a>

            <div class="nav-group-title">Abonnements</div>
            <a href="{{ route('client.subscriptions.index') }}" class="{{ request()->routeIs('client.subscriptions.*') ? 'active' : '' }}">
                <i class="fas fa-rotate"></i>
                Mes abonnements
            </a>

            <div class="nav-group-title">Support</div>
            <a href="{{ route('client.tickets.index') }}" class="{{ request()->routeIs('client.tickets.index') || request()->routeIs('client.tickets.show') ? 'active' : '' }}">
                <i class="fas fa-headset"></i>
                Mes tickets
            </a>
            <a href="{{ route('client.tickets.create') }}" class="{{ request()->routeIs('client.tickets.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i>
                Nouveau ticket
            </a>

            <div class="nav-group-title">Profil</div>
            <a href="{{ route('client.profile.edit') }}" class="{{ request()->routeIs('client.profile.edit') ? 'active' : '' }}">
                <i class="fas fa-user-gear"></i>
                Modifier mon profil
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">
                <i class="fas fa-right-from-bracket"></i>
                D&eacute;connexion
            </button>
        </form>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <span class="client-kicker"><i class="fas fa-circle-check"></i> Compte client</span>
                <h1>@yield('page_title', 'Espace client')</h1>
                <p>@yield('page_subtitle', 'Mon espace client')</p>
            </div>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
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
