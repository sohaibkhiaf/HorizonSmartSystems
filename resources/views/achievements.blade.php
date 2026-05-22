@extends('layouts.app')

@section('title', 'Réalisations — Horizon Smart Systems')

@section('style')
<link rel="stylesheet" href="{{ asset('css/achievements.css') }}">
@endsection

@section('content')


<section class="hero">
    <div class="hero-container">

        <!-- Left Side -->
        <div class="hero-text">

            <h1 class="hero-title">
                Nos réalisations
            </h1>

            <p class="hero-description">
                Découvrez quelques projets web, logiciels et solutions digitales conçus pour accompagner la réussite de nos clients.</p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">contactez-nous &nbsp;<i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hero-image">
            <img src="{{ asset('images/hero_image.png') }}" alt="Hero Image">
        </div>
    </div>
</section>




<!-- Method Section -->
<section class="features">
    <div class="features-container">

        <!-- Left Content -->
        <div class="features-left">
            <h2 class="features-title">Des solutions concretes, pensees pour des besoins reels.</h2>

            <p class="features-description">
                Chaque projet est unique. Nous concevons des solutions adaptees aux objectifs, au secteur et aux utilisateurs de nos clients.
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
                        <p>Des sites modernes, rapides et performats.</p>
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
                        <p>Des sites modernes, rapides et performats.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="card-text">
                        <h3>Systemes de gestion</h3>
                        <p>Solutions completes pour optimiser votre activite.</p>
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
                        <p>Accompagnement continu et amelioration continue.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Tabs Section -->
<section class="projects-tabs-section">
    <div class="tabs-container">

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" data-type="all">
                <i class="fa-solid fa-layer-group"></i>
                Tous
            </button>

            <button class="tab" data-type="web">
                <i class="fa-solid fa-globe"></i>
                Sites web
            </button>

            <button class="tab" data-type="management">
                <i class="fa-solid fa-diagram-project"></i>
                Gestion
            </button>

            <button class="tab" data-type="ecommerce">
                <i class="fa-solid fa-cart-shopping"></i>
                E-commerce
            </button>
        </div>

        <!-- Projects (example structure) -->
<div class="projects-grid">

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Site web vitrine</h3>
                <span class="type">Web</span>
            </div>
            <p>Création de sites modernes et rapides adaptés à tous les écrans.</p>

            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
    </div>

    <div class="acheivement-card" data-type="ecommerce">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Shop en ligne</h3>
                <span class="type">E-commerce</span>
            </div>
            <p>Plateforme e-commerce performante avec gestion complète des produits.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="management">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>ERP Gestion</h3>
                <span class="type">Gestion</span>
            </div>
            <p>Solution de gestion interne pour optimiser les opérations de l’entreprise.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Landing page</h3>
                <span class="type">Web</span>
            </div>
            <p>Pages d’atterrissage optimisées pour conversion et performance SEO.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Site web vitrine</h3>
                <span class="type">Web</span>
            </div>
            <p>Création de sites modernes et rapides adaptés à tous les écrans.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="ecommerce">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Shop en ligne</h3>
                <span class="type">E-commerce</span>
            </div>
            <p>Plateforme e-commerce performante avec gestion complète des produits.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
    </div>

    <div class="acheivement-card" data-type="management">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>ERP Gestion</h3>
                <span class="type">Gestion</span>
            </div>
            <p>Solution de gestion interne pour optimiser les opérations de l’entreprise.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Landing page</h3>
                <span class="type">Web</span>
            </div>
            <p>Pages d’atterrissage optimisées pour conversion et performance SEO.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Site web vitrine</h3>
                <span class="type">Web</span>
            </div>
            <p>Création de sites modernes et rapides adaptés à tous les écrans.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="ecommerce">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_3.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Shop en ligne</h3>
                <span class="type">E-commerce</span>
            </div>
            <p>Plateforme e-commerce performante avec gestion complète des produits.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="management">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_2.webp') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>ERP Gestion</h3>
                <span class="type">Gestion</span>
            </div>
            <p>Solution de gestion interne pour optimiser les opérations de l’entreprise.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="acheivement-card" data-type="web">
        <div class="acheivement-image">
            <img src="{{ asset('images/achievement_1.jpg') }}" alt="">
        </div>

        <div class="acheivement-content">
            <div class="acheivement-header">
                <h3>Landing page</h3>
                <span class="type">Web</span>
            </div>
            <p>Pages d’atterrissage optimisées pour conversion et performance SEO.</p>
            <a href="#" class="plus-link">
                voir le projet <i class="fas fa-arrow-right"></i>
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
                <h2>Prêt à concretiser votre projet ?  </h2>
                <h3>Discutons de vos besoins et construsions ensemble une solution efficace et sur mesure.</h3>
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


