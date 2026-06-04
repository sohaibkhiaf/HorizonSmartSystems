<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panneau Opérateur')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="admin-wrapper">

    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <h2>HSS Opérateur</h2>
                <p>Gestionnaire des ventes</p>
            </div>
        </div>

        <nav class="admin-nav">
            <div class="nav-group-title">Opérateur</div>

            <a href="{{ route('operator.products') }}"
            class="{{ request()->routeIs('operator.products') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                Produits
            </a>

            <a href="{{ route('operator.requests') }}"
            class="{{ request()->routeIs('operator.requests') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i>
                Mes demandes
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
                <h1>@yield('page_title', 'Panneau Opérateur')</h1>
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

