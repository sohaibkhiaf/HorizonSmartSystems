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
            <p>Une idee, un besoin, un projet digital? 
                <br> Notre equipe est a votre ecoute pour vous accompagner.
            </p>
        </div>
    </div>
</section>



@endsection

