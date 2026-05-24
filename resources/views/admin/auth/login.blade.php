<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-icon">
        <i class="fas fa-lock"></i>
    </div>

    <h1>Admin Login</h1>
    <p>Access your website management panel</p>

    @if($errors->any())
        <div class="alert danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <label class="checkbox-row">
            <input type="checkbox" name="remember">
            <span>Remember me</span>
        </label>

        <button type="submit" class="btn-main full">
            Login
        </button>
    </form>
</div>

</body>
</html>

