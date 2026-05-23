@extends('layouts.app')

@section('title', 'Contact')

@section('style')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection


@section('content')

<!-- Hero Banner -->
<section class="hero-about">
    <div class="hero-overlay">
        <div class="hero-content">
            <p>CONTACTEZ-NOUS</p>
            <h1>Parlons de votre projet</h1>
            <p>Une idée, un besoin, un projet digital ? 
                <br> Notre équipe est à votre écoute pour vous accompagner.
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="contact-container">

        <!-- LEFT: CONTACT INFOS -->
        <div class="contact-info">

            <h2 class="contact-title">Contactez-nous</h2>

            <!-- Item -->
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>Téléphone</h4>
                    <p>0561 65 51 00 | 0782 94 07 60</p>
                </div>
            </div>

            <!-- Item -->
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p>contact@hss-agency.com</p>
                </div>
            </div>

            <!-- Item -->
            <div class="contact-item">
                <i class="fab fa-facebook-f"></i>
                <div>
                    <h4>Facebook</h4>
                    <p>facebook.com/HorizonSmartSystems</p>
                </div>
            </div>

            <!-- Item -->
            <div class="contact-item">
                <i class="fas fa-location-dot"></i>
                <div>
                    <h4>Adresse</h4>
                    <p>Rue Zaatcha, en face du centre des impôts</p>
                </div>
            </div>

        </div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="contact-form">

            <h2>Envoyez-nous un message</h2>

            <form>
                <div class="form-grid">

                    <div class="form-group">
                        <label>Nom complet (*)</label>
                        <input type="text" placeholder="Votre nom" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="votre@email.com">
                    </div>

                    <div class="form-group">
                        <label>Téléphone (*)</label>
                        <input type="tel" placeholder="+213 XXX XX XX XX" required>
                    </div>

                    <div class="form-group">
                        <label>Sujet (*)</label>
                        <input type="text" placeholder="Décrivez brièvement l’objet de votre demande" required>
                    </div>

                </div>

                <div class="form-group message-group">
                    <label>Message (*)</label>
                    <textarea placeholder="Décrivez votre projet ou votre besoin en détail..." required></textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="consent">
                    <label for="consent">
                        J'accepte d'être contacté par Horizon Smart Systems.
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    Envoyer le message <i class="fa-regular fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>
</section>

<!-- Map & Hours Section -->

<section class="map-hours-section">

    <div class="map-hours-box">

        <!-- MAP -->
        <div class="map-part">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3274.1601452089976!2d5.7204922742544975!3d34.85220317286611!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12f509002f0dc033%3A0x427675b9fa5b3b87!2sHORIZON%20PC%20INFORMATIQUE!5e0!3m2!1sen!2sdz!4v1779480198528!5m2!1sen!2sdz"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

        <!-- HOURS -->
        <div class="hours-part">
            <div class="hours-header">
                <i class="fas fa-clock"></i>
                <h3>Horaires d'ouverture</h3>
            </div>

            <div class="hours-content">
                <div class="hour-row">
                    <span>Samedi</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row">
                    <span>Dimanche</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row">
                    <span>Lundi</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row">
                    <span>Mardi</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row">
                    <span>Mercredi</span>
                    <span>08:30 - 16:30</span>
                </div>

                <div class="hour-row closed">
                    <span>Jeudi</span>
                    <span>Fermé</span>
                </div>

                <div class="hour-row closed">
                    <span>Vendredi</span>
                    <span>Fermé</span>
                </div>
            </div>
        </div>

    </div>

</section>

@endsection

