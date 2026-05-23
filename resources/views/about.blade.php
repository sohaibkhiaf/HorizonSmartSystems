@extends('layouts.app')

@section('title', 'À propos — Horizon Smart Systems')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
@endsection

@section('content')

<!-- Hero Banner -->
<section class="hero-about">
    <div class="hero-overlay">
        <div class="hero-content">
            <h1>À propos de HSS</h1>
            <p>Votre partenaire en solutions web et digitales.</p>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <p class="hero-small">QUI SOMMES-NOUS ?</p>

            <h1 class="hero-title">
                Horizon Smart Systems
            </h1>
            <h3 class="hero-subtitle">
                Votre partenaire digital de confiance.
            </h3>

            <p class="hero-description">
                Horizon Smart Systems (HSS) est une agence spécialisée dans le développement web, logiciel et les solutions digitales. Nous accompagnons les entreprises, commerçants et professionnels dans la création d'outils modernes, performants et adaptés à leurs objectifs.
                <br>
                <br>
                Notre mission est simple : fournir des solutions innovantes qui optimisent vos processus, améliorent votre productivité et contribuent à la croissance durable de votre activité.
            </p>

        </div>

        <div class="hero-image">
            <img src="{{ asset('images/hss_agency.png') }}" alt="Development Agency">
        </div>
    </div>
</section>


<!-- Values Section -->
<section class="values">
    <div class="values-container">
        <!-- Notre vision Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fas fa-lightbulb"></i>
                </div>

                <div class="card-text">
                    <h3>Notre vision</h3>
                    <p>Devenir un acteur incontournable des solutions digitales en offrant des services innovants, fiables et accessibles à tous.</p>
                </div>
            </div>
            
        </div>

        <!-- Notre mission Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div class="card-text">
                    <h3>Notre mission</h3>
                    <p>Accompagner nos clients avec des solutions sur mesure qui répondent réellement à leurs besoins et leur apportent de la valeur.</p>
                </div>
            </div>
            
        </div>

        <!-- Nos valeurs Card -->
        <div class="values-card">
            <div class="card-content">
                <div class="icon-circle">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div class="card-text">
                    <h3>Nos valeurs</h3>
                    <p>Innovation
                        Qualité
                        Fiabilité
                        Transparence
                        Engagement
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- Team Section -->
<section class="team">
    <div class="team-container">

        <div class="team-left">
            <h2>Notre équipe</h2>
            <p>
                Notre équipe est composée de développeurs, designers, intégrateurs et experts en solutions digitales passionnés par la technologie et l'innovation.
                <br>
                <br>
                Nous travaillons ensemble pour transformer vos idées en produits performants, ergonomiques et adaptés à votre domaine d'activité.
            </p>

            <a href="/contact" class="team-btn">
                Contactez-nous <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="team-right">

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Sohaib K</h3>
                <span>Programmeur</span>

                <div class="socials">
                    <i class="fa-brands fa-facebook-f"></i>
                    <i class="fa-brands fa-linkedin-in"></i>
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Amine S</h3>
                <span>Designer</span>

                <div class="socials">
                    <i class="fa-brands fa-facebook-f"></i>
                    <i class="fa-brands fa-linkedin-in"></i>
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>

            <div class="team-card">
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3>Said</h3>
                <span>Programmeur</span>

                <div class="socials">
                    <i class="fa-brands fa-facebook-f"></i>
                    <i class="fa-brands fa-linkedin-in"></i>
                    <i class="fa-solid fa-envelope"></i>
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
                <h2>Vous avez un projet ? <br> Nous sommes là pour le réaliser. </h2>
                <h3>Discutons de vos idées et transformons-les en réalité.</h3>
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

