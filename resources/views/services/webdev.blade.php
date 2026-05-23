@extends('layouts.app')

@section('title', 'Création de sites web — Horizon Smart Systems')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/services/webdev.css') }}">
@endsection

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">

        <div class="hero-text">
            <h1 class="hero-title">
                Création de sites web professionnels
            </h1>

            <p class="hero-description">
                Nous concevons des sites web modernes, rapides et responsive, adaptés à votre activité, pour renforcer votre présence en ligne et attirer plus de clients.
            </p>

            <div class="hero-buttons">
                <a href="/contact" class="btn btn-primary">
                    Contactez-nous &nbsp;<i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/webdev.png') }}" alt="Création de sites web">
        </div>

    </div>
</section>


<!-- Description Section -->
<section class="web-description">
    <div class="web-description-container">
        <h2>Développement web adapté à vos besoins</h2>

        <p>
            Un site web est bien plus qu’une simple vitrine. C’est un outil professionnel qui présente votre entreprise, rassure vos clients et facilite le contact avec votre audience.
            Chez Horizon Smart Systems, nous créons des sites web clairs, performants et faciles à utiliser, selon les objectifs de votre projet.
        </p>
    </div>
</section>


<!-- Website Types Section -->
<section class="website-types">

    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <h2>Sites web d’entreprise et professionnels</h2>
                <p>Idéal pour présenter votre activité, vos services, vos réalisations et vos informations de contact.</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>Fonctionnalités de base</h3>

                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> Design moderne et responsive</li>
                    <li><i class="fa-solid fa-circle-check"></i> Page d’accueil professionnelle</li>
                    <li><i class="fa-solid fa-circle-check"></i> Pages services, à propos et contact</li>
                    <li><i class="fa-solid fa-circle-check"></i> Formulaire de contact</li>
                    <li><i class="fa-solid fa-circle-check"></i> Intégration Google Maps</li>
                    <li><i class="fa-solid fa-circle-check"></i> Optimisation de base pour les moteurs de recherche</li>
                </ul>
            </div>

            <div class="price-box">
                <span>Prix de base</span>
                <h3>À partir de 35 000 DA</h3>
                <p>Le prix peut varier selon le nombre de pages et les fonctionnalités demandées.</p>
            </div>
        </div>
    </div>


    <div class="website-type-card">
        <div class="type-header">
            <div class="type-icon">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>

            <div>
                <h2>Développement de sites e-commerce</h2>
                <p>Une solution complète pour présenter vos produits, recevoir des commandes et développer vos ventes en ligne.</p>
            </div>
        </div>

        <div class="type-content">
            <div class="features-box">
                <h3>Fonctionnalités de base</h3>

                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> Interface moderne et responsive</li>
                    <li><i class="fa-solid fa-circle-check"></i> Catalogue de produits</li>
                    <li><i class="fa-solid fa-circle-check"></i> Pages catégories et détails produit</li>
                    <li><i class="fa-solid fa-circle-check"></i> Panier d’achat</li>
                    <li><i class="fa-solid fa-circle-check"></i> Formulaire de commande</li>
                    <li><i class="fa-solid fa-circle-check"></i> Tableau de gestion simple</li>
                </ul>
            </div>

            <div class="price-box">
                <span>Prix de base</span>
                <h3>À partir de 75 000 DA</h3>
                <p>Le prix dépend du nombre de produits, des options de paiement et des fonctionnalités avancées.</p>
            </div>
        </div>
    </div>

</section>


<!-- Call To Action Section -->
<section class="cta">
    <div class="cta-overlay">

        <div class="cta-container">

            <div class="cta-left">
                <h2>Vous avez un projet de site web ?</h2>
                <h3>Contactez-nous pour discuter de vos besoins et obtenir une proposition adaptée.</h3>
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

