@extends('layouts.app')

@section('title', 'Réalisations — Horizon Smart Systems')

@section('style')
<link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">

            <h1 class="hero-title">
                Nos réalisations
            </h1>

            <p class="hero-description">
                Découvrez quelques projets web, logiciels et solutions digitales conçus pour accompagner la réussite de nos clients.</p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">Contactez-nous &nbsp;<i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hero_image_realisations.png') }}" alt="Hero Image">
        </div>
    </div>
</section>


<!-- Method Section -->
<section class="features">
    <div class="features-container">

        <!-- Left Content -->
        <div class="features-left">
            <h2 class="features-title">Des solutions concrètes, pensées pour des besoins réels.</h2>

            <p class="features-description">
                Chaque projet est unique. Nous concevons des solutions adaptées aux objectifs, au secteur et aux utilisateurs de nos clients.
            </p>
        </div>

        <!-- Right Cards -->
        <div class="features-right">

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-globe"></i>
                    </div>

                    <div class="card-text">
                        <h3>Sites web</h3>
                        <p>Des sites modernes, rapides et performants.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="card-text">
                        <h3>Sites E-commerce</h3>
                        <p>Sites e-commerce avec catalogue, panier, commandes et gestion simple.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="card-text">
                        <h3>Systèmes de gestion</h3>
                        <p>Solutions complètes pour optimiser votre activité.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <div class="card-text">
                        <h3> Support & optimisation</h3>
                        <p>Accompagnement continu et amélioration continue.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Projects Section -->
<section class="projects-tabs-section">

    <div class="tabs-box">
        <div class="tabs">
            <div class="tab active" data-type="all">
                <i class="fa-solid fa-layer-group"></i>
                Tous
            </div>

            <div class="tab" data-type="web">
                <i class="fa-solid fa-globe"></i>
                Sites Web
            </div>

            <div class="tab" data-type="app">
                <i class="fa-solid fa-code"></i>
                Applications
            </div>

            <div class="tab" data-type="software">
                <i class="fa-solid fa-laptop-code"></i>
                Logiciels
            </div>
        </div>
    </div>

    <div class="projects-box">
        <div class="projects-grid">

            <div class="acheivement-card" data-type="web">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Site vitrine</h3>
                        <span class="type">Web</span>
                    </div>

                    <p>Création d’un site moderne pour présenter les services d’une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="app">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_2.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application métier</h3>
                        <span class="type">App</span>
                    </div>

                    <p>Développement d’une solution personnalisée adaptée aux besoins du client.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="software">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>

                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="web">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Site vitrine</h3>
                        <span class="type">Web</span>
                    </div>

                    <p>Création d’un site moderne pour présenter les services d’une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="app">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_2.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application métier</h3>
                        <span class="type">App</span>
                    </div>

                    <p>Développement d’une solution personnalisée adaptée aux besoins du client.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="software">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>

                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="web">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Site vitrine</h3>
                        <span class="type">Web</span>
                    </div>

                    <p>Création d’un site moderne pour présenter les services d’une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="app">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_2.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application métier</h3>
                        <span class="type">App</span>
                    </div>

                    <p>Développement d’une solution personnalisée adaptée aux besoins du client.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="acheivement-card" data-type="software">
                <div class="acheivement-image">
                    <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>

                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>

                    <a class="view-link">
                        Voir le projet
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

</section>


<!-- Call To Action Section -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <!-- LEFT -->
            <div class="cta-left">
                <h2>Prêt à concrétiser votre projet ?  </h2>
                <h3>Discutons de vos besoins et construisons ensemble une solution efficace et sur mesure.</h3>
            </div>

            <!-- RIGHT -->
            <div class="cta-right">
                <a href="/contact" class="cta-btn">
                    Contactez-nous <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>

    </div>
</section>


@endsection

@section('script')
    <script type="module" src="{{ asset('js/achievements.js') }}"></script>
@endsection

