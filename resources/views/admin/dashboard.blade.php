@extends('admin.layouts.app-admin')

@section('title', 'Tableau de bord Admin')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble du contenu de votre site')

@section('content')

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <div>
            <h3>{{ $achievementsCount }}</h3>
            <p>Total des réalisations</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-eye"></i>
        </div>
        <div>
            <h3>{{ $publishedAchievementsCount }}</h3>
            <p>Réalisations publiées</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <h3>{{ $waitingMessagesCount }}</h3>
            <p>Messages en attente</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-check"></i>
        </div>
        <div>
            <h3>{{ $seenMessagesCount }}</h3>
            <p>Messages consultés</p>
        </div>
    </div>

</div>

<div class="admin-tabs">
    <a href="{{ route('admin.achievements.create') }}" class="tab-card">
        <i class="fas fa-plus"></i>
        <h3>Créer une réalisation</h3>
        <p>Ajouter un nouveau projet à votre site.</p>
    </a>

    <a href="{{ route('admin.achievements.index') }}" class="tab-card">
        <i class="fas fa-pen"></i>
        <h3>Modifier une réalisation</h3>
        <p>Mettre à jour, publier ou supprimer des projets existants.</p>
    </a>

    <a href="{{ route('admin.messages.waiting') }}" class="tab-card">
        <i class="fas fa-envelope"></i>
        <h3>Messages en attente</h3>
        <p>Consulter les nouveaux messages de contact.</p>
    </a>

    <a href="{{ route('admin.messages.seen') }}" class="tab-card">
        <i class="fas fa-check-circle"></i>
        <h3>Messages consultés</h3>
        <p>Gérer les messages déjà examinés.</p>
    </a>
</div>

@endsection

