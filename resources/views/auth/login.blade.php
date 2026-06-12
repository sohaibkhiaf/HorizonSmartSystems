@extends('layouts.app')

@section('title','Connextion')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')


<div class="container">
    <div class="icon">
        <i class="fas fa-lock"></i>
    </div>

    <h1>Connexion Utilisateur</h1>
    <p>Accédez à votre panneau de gestion du site</p>

    @if($errors->any())
        <div class="alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="form-group">
            <label>E-mail</label>
            <input 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="Entrez votre e-mail"
                required 
                autofocus>
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input 
                type="password" 
                name="password" 
                placeholder="Entrez votre mot de passe"
                required>
        </div>

        <label class="checkbox-row">
            <input type="checkbox" name="remember">
            <span>Se souvenir de moi</span>
        </label>

        <button type="submit" class="login-btn full">
            Se connecter
        </button>
    </form>
</div>

@endsection

