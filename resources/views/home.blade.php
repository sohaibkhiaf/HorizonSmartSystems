@extends('layouts.app')

@section('title', 'Home')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <!-- Left Side -->
        <div class="hero-text">
            <p class="hero-small">Agence de développement web & logiciel</p>

            <h1 class="hero-title">
                Des solutions digitales modernes pour développer votre activité
            </h1>

            <p class="hero-description">
                Horizon Smart Systems accompagne les entreprises et organisations dans leur transformation digitale
                grâce à des solutions web et logiciel fiables, sécurisées et parfaitement adaptées à leurs besoins.
            </p>

            <div class="hero-buttons">
                <a href="/devis" class="btn btn-primary">demander un devis &nbsp;<i class="fa-solid fa-arrow-right"></i></a>
                <a href="/services" class="btn btn-outline">voir nos services &nbsp;<i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hero-image">
            <img src="{{ asset('images/hero_image.png') }}" alt="Hero Image">
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about">
    <div class="about-container">

        <!-- Left Content -->
        <div class="about-text">
            <h2 class="about-title">Qui sommes-nous ?</h2>

            <p class="about-description">
                Horizon Smart Systems est un bureau de développement logiciel et de sites web basé sur l'innovation,
                la qualité et la proximité client
            </p>
        </div>

        <!-- Right Cards -->
        <div class="about-cards">

            <!-- Innovation Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-lightbulb"></i>
                    </div>

                    <div class="card-text">
                        <h3>Innovation</h3>
                        <p>Des technologies modernes au service de vos idées</p>
                    </div>
                </div>
                
            </div>

            <!-- Performance Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-text">
                        <h3>Performance</h3>
                        <p>Des solutions efficaces pour des résultats mesurables</p>
                    </div>
                </div>
                
            </div>

            <!-- Confiance Card -->
            <div class="about-card">
                <div class="card-content">
                    <div class="icon-circle">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="card-text">
                        <h3>Confiance</h3>
                        <p>Un partenaire fiable pour construire dans la durée</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Proposed Services Section -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">Services proposés</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-globe"></i></div>
                    <h3>Création de sites web</h3>
                    <p>Sites web modernes, rapides et responsive, conçus pour valoriser votre activité.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>Création d'application sur mesure</h3>
                    <p>Applications web et desktop sur mesure adaptées à vos besoins spécifiques.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Gestion commerciale, paie et autres solutions</h3>
                    <p>Solutions intégrées pour gérer votre activité et optimiser vos processus métiers.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-cogs"></i></div>
                    <h3>Systèmes de gestion personnalisés</h3>
                    <p>Logiciels de gestion sur mesure pour un meilleur suivi et une productivité accrue.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-headset"></i></div>
                    <h3>Assistance logicielle et informatique</h3>
                    <p>Support technique, résolution de problèmes et réseaux.</p>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Other Services Section -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">Autres services</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-earth-europe"></i></div>
                    <h3>Acquisition de logiciels internationaux</h3>
                    <p>Fourniture de logiciels internationaux selon vos besoins et budget.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                    <h3>Abonnements logiciels</h3>
                    <p>Proposition et gestion d'abonnements IA (ChatGPT, Gemini...), Steam Cards, Adobe, Microsoft...</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>Hébergement de sites web</h3>
                    <p>Hébergement sécurisé, performant et évolutif pour vos sites et applications.</p>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-circle-info"></i></div>
                    <h3>Conseil et accompagnement</h3>
                    <p>Conseil stratégique et accompagnement à chaque étape de la réussite de vos projets.</p>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="features-container">

        <!-- Left Content -->
        <div class="features-left">
            <h2 class="features-title">Pourquoi choisir HSS ?</h2>
        </div>

        <!-- Right Cards -->
        <div class="features-right">

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>

                    <div class="card-text">
                        <h3>Solutions sur mesure</h3>
                        <p>Adaptées à vos besoins et budgets.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-text">
                        <h3>Qualité et fiabilité</h3>
                        <p>Des services fiables et de haute qualité.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="card-text">
                        <h3>Rapidité et efficacité</h3>
                        <p>Des solutions rapides pour un meilleur rendement.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="card-text">
                        <h3>Support professionnel</h3>
                        <p>Une équipe à votre écoute pour vous accompagner.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="acheivements">
<div class="acheivements-header">
    <h2 class="acheivements-title">Nos réalisations</h2>

    <a href="#" class="acheivements-link">
        voir toutes nos réalisations <i class="fas fa-arrow-right"></i>
    </a>
</div>

    <div class="swiper acheivements-swiper">
        <div class="swiper-wrapper">

            <div class="swiper-slide acheivement-card">
                
                <!-- Image -->
                <div class="acheivement-image">
                    <img src="{{ asset('images/images.jpg') }}" alt="">
                </div>

                <!-- Content -->
                <div class="acheivement-content">

                    <div class="acheivement-header">
                        <h3>Site E-commerce</h3>
                        <span class="type">Web App</span>
                    </div>

                    <p>Création d'une plateforme e-commerce moderne avec gestion complète des produits et commandes.</p>

                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project2.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>
                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>
                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application mobile</h3>
                        <span class="type">Mobile</span>
                    </div>
                    <p>Application mobile moderne avec interface fluide et performance optimisée.</p>
                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                
                <!-- Image -->
                <div class="acheivement-image">
                    <img src="{{ asset('images/images.jpg') }}" alt="">
                </div>

                <!-- Content -->
                <div class="acheivement-content">

                    <div class="acheivement-header">
                        <h3>Site E-commerce</h3>
                        <span class="type">Web App</span>
                    </div>

                    <p>Création d'une plateforme e-commerce moderne avec gestion complète des produits et commandes.</p>

                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project2.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>
                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>
                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application mobile</h3>
                        <span class="type">Mobile</span>
                    </div>
                    <p>Application mobile moderne avec interface fluide et performance optimisée.</p>
                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                
                <!-- Image -->
                <div class="acheivement-image">
                    <img src="{{ asset('images/images.jpg') }}" alt="">
                </div>

                <!-- Content -->
                <div class="acheivement-content">

                    <div class="acheivement-header">
                        <h3>Site E-commerce</h3>
                        <span class="type">Web App</span>
                    </div>

                    <p>Création d'une plateforme e-commerce moderne avec gestion complète des produits et commandes.</p>

                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project2.jpg') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Système de gestion</h3>
                        <span class="type">ERP</span>
                    </div>
                    <p>Solution de gestion interne pour optimiser les opérations d'une entreprise.</p>
                </div>
            </div>

            <div class="swiper-slide acheivement-card">
                <div class="acheivement-image">
                    <img src="{{ asset('images/project.webp') }}" alt="">
                </div>

                <div class="acheivement-content">
                    <div class="acheivement-header">
                        <h3>Application mobile</h3>
                        <span class="type">Mobile</span>
                    </div>
                    <p>Application mobile moderne avec interface fluide et performance optimisée.</p>
                </div>
            </div>

        </div>
    </div>
</section>


<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <!-- LEFT -->
            <div class="cta-left">
                <h2>Un projet en tête ?</h2>
                <h3>Parlons-en dès aujourd'hui.</h3>
            </div>

            <!-- CENTER -->
            <div class="cta-center">
                <p>
                    Recevez une réponse rapide et une proposition adaptée à vos besoins.
                </p>
            </div>

            <!-- RIGHT -->
            <div class="cta-right">
                <a href="/devis" class="cta-btn">
                    Demander un devis <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>

    </div>
</section>


@endsection


@section('script')
    <script type="module" src="{{ asset('js/home.js') }}"></script>
@endsection

