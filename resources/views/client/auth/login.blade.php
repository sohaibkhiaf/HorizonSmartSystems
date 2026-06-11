<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Connexion Client</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('images/hss_logo_icon.png')}}"/>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-body">

<div class="container">
    <div class="icon">
        <i class="fas fa-lock"></i>
    </div>

    <h1>Connexion Client</h1>
    <p>Accédez à votre panneau de gestion du site</p>

    @if($errors->any())
        <div class="alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('client.login.submit') }}">
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

</body>
</html>

