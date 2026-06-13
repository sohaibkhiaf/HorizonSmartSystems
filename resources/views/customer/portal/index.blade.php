@extends('layouts.store')

@section('title', 'Espace client')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customer/portal.css') }}">
@endsection

@section('content')

<section class="portal-section">

    <div class="portal-header">
        <div>
            <h1>Espace client</h1>
            <p>Consultez vos démos, mettez à jour vos informations et contactez l’administration.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn">
                Déconnexion
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="portal-grid">

        <div class="portal-card">
            <h2>Mes informations</h2>

            <form method="POST" action="{{ route('customer.portal.contact.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" value="{{ $user->name }}" readonly>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" value="{{ $user->email }}" readonly>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="Votre numéro de téléphone">
                </div>

                <div class="form-group">
                    <label>Entreprise</label>
                    <input type="text"
                           name="company"
                           value="{{ old('company', $user->company) }}"
                           placeholder="Nom de votre entreprise">
                </div>

                <button type="submit" class="btn-main">
                    Mettre à jour
                </button>
            </form>
        </div>

        <div class="portal-card">
            <h2>Mes démos</h2>

            @forelse($demos as $demo)
                <div class="demo-item">
                    <div>
                        <h3>{{ $demo->name }}</h3>
                        <p>Démo web disponible</p>
                    </div>

                    <a href="{{ $demo->demo_link }}" target="_blank" class="demo-link">
                        Ouvrir
                    </a>
                </div>
            @empty
                <p class="empty-text">Aucune démo disponible pour le moment.</p>
            @endforelse
        </div>

        <div class="portal-card chat-card">
            <h2>Contact admin</h2>

            <div class="chat-box">
                @forelse($messages as $message)
                    <div class="chat-message {{ $message->sender_type === 'customer' ? 'customer' : 'admin' }}">
                        <div class="message-content">
                            <p>{{ $message->message }}</p>
                            <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="empty-text">Aucun message pour le moment.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('customer.portal.chat.send') }}" class="chat-form">
                @csrf

                <textarea name="message"
                          placeholder="Écrivez votre message..."
                          required>{{ old('message') }}</textarea>

                <button type="submit" class="btn-main">
                    Envoyer
                </button>
            </form>
        </div>

    </div>

</section>

@endsection


