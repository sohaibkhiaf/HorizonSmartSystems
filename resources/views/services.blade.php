@extends('layouts.app')

@section('title', 'Nos services — Horizon Smart Systems')

@section('style')
<link rel="stylesheet" href="{{ asset('css/services.css') }}">
@endsection


@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <!-- Left Side -->
        <div class="hero-text">

            <h1 class="hero-title">
                Nos services
            </h1>

            <p class="hero-description">
                Des solutions digitales complètes et sur mesure pour accompagner la croissance de votre entreprise et optimiser vos performances.
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">Contactez-nous &nbsp;<i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Right Side -->
        <div class="hero-image">
            <img src="{{ asset('images/hero_image_nos_services.png') }}" alt="Hero Image">
        </div>
    </div>
</section>


<!-- Proposed Services Section -->
<section class="services">
    <div class="services-container">

        <h2 class="services-title">Nos services principaux</h2>

        <div class="swiper services-swiper">
            <div class="swiper-wrapper">

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-globe"></i></div>
                    <h3>Création de sites web</h3>
                    <p>Sites web modernes, rapides et responsive, conçus pour valoriser votre activité.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>Création d'application sur mesure</h3>
                    <p>Applications web et desktop sur mesure adaptées à vos besoins spécifiques.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Gestion commerciale, paie et autres solutions</h3>
                    <p>Solutions intégrées pour gérer votre activité et optimiser vos processus métiers.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-cogs"></i></div>
                    <h3>Systèmes de gestion personnalisés</h3>
                    <p>Logiciels de gestion sur mesure pour un meilleur suivi et une productivité accrue.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fas fa-headset"></i></div>
                    <h3>Assistance logicielle et informatique</h3>
                    <p>Support technique, résolution de problèmes et réseaux.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
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

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                    <h3>Abonnements logiciels</h3>
                    <p>Proposition et gestion d'abonnements IA (ChatGPT, Gemini...), Steam Cards, Adobe, Microsoft...</p>
                
                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>Hébergement de sites web</h3>
                    <p>Hébergement sécurisé, performant et évolutif pour vos sites et applications.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="swiper-slide service-card">
                    <div class="icon"><i class="fa-solid fa-circle-info"></i></div>
                    <h3>Conseil et accompagnement</h3>
                    <p>Conseil stratégique et accompagnement à chaque étape de la réussite de vos projets.</p>

                    <a href="#" class="plus-link">
                        En savoir plus <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>


<!-- Method Section -->
<section class="method">
    <div class="method-container">

        <div class="method-left">
            <h2 class="method-title">Une approche simple et transparente</h2>

            <p class="method-description">
                De l'analyse de vos besoins à la livraison, nous suivons un processus clair pour garantir la réussite de votre projet.
            </p>
        </div>

        <div class="method-right">

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-1"></i>
                    </div>

                    <div class="card-text">
                        <h3>Analyse & découverte <i class="fa-solid fa-magnifying-glass"></i></h3>
                        <p>Nous analysons vos besoins, vos objectifs et votre marché pour définir la meilleure approche.</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-2"></i>
                    </div>
                    <div class="card-text">
                        <h3>Conception & planification <i class="fa-regular fa-pen-to-square"></i></h3>
                        <p>Nous concevons l'architecture et planifions les étapes clés de votre projet.</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-3"></i>
                    </div>
                    <div class="card-text">
                        <h3> Développement & tests <i class="fa-solid fa-code"></i></h3>
                        <p>Nous développons, testons et assurons la qualité tout au long du processus.</p>
                    </div>
                </div>
            </div>

            <div class="method-card">
                <div class="card-inner">
                    <div class="icon">
                        <i class="fa-solid fa-4"></i>
                    </div>
                    <div class="card-text">
                        <h3> Livraison & suivi <i class="fa-solid fa-rocket"></i></h3>
                        <p>Nous livrons votre solution et restons à vos côtés pour assurer son évolution.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Call To Action Section -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>Prêt à démarrer votre projet ?  </h2>
                <h3>Parlons de vos besoins et construisons ensemble la meilleure solution.</h3>
            </div>

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
    <script type="module" src="{{ asset('js/services.js') }}"></script>
@endsection

