@extends('admin.layouts.admin')

@section('title', 'Nos réalisations')
@section('page_title', 'Nos réalisations')
@section('page_subtitle', 'Gérer tous les projets du site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_achievements/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.achievements.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer une réalisation
    </a>
</div>

<div class="achievements-grid">

    @forelse($achievements as $achievement)
        <div class="achievement-card">

            <div class="achievement-image">
                @if($achievement->image)
                    <img src="{{ asset('uploads/' . $achievement->image) }}" alt="{{ $achievement->fr_title }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-briefcase"></i>
                    </div>
                @endif
            </div>

            <div class="achievement-content">

                <div class="card-top">
                    <div>
                        <h3>{{ $achievement->fr_title }}</h3>

                        <div class="card-badges">
                            @if($achievement->is_published)
                                <span class="badge success">
                                    <i class="fas fa-eye"></i>
                                    Publié
                                </span>
                            @else
                                <span class="badge muted">
                                    <i class="fas fa-eye-slash"></i>
                                    Masqué
                                </span>
                            @endif

                            <span class="badge type">
                                {{ $achievement->fr_type }}
                            </span>

                            @if($achievement->project_url)
                                <a href="{{ $achievement->project_url }}" target="_blank" class="badge link">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                    Lien
                                </a>
                            @else
                                <span class="badge muted">
                                    Sans lien
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <p class="description">
                    {{ Str::limit($achievement->fr_short_description, 140) }}
                </p>

                <div class="card-actions">
                    <a href="{{ route('admin.achievements.edit', $achievement) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.achievements.destroy', $achievement) }}" onsubmit="return confirm('Supprimer cette réalisation ?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="action-btn delete">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </form>
                </div>

            </div>

        </div>
    @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-briefcase"></i>
            </div>

            <h3>Aucune réalisation trouvée</h3>
            <p>Vous n’avez pas encore ajouté de projet à afficher sur le site.</p>

            <a href="{{ route('admin.achievements.create') }}" class="empty-link">
                <i class="fas fa-plus"></i>
                Créer une réalisation
            </a>
        </div>
    @endforelse

</div>

@if($achievements->hasPages())
    <div class="navigator-container">
        @if ($achievements->currentPage() > 1)
            <a href="{{ $achievements->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $achievements->url($achievements->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $achievements->lastPage(); $i++)
            <a href="{{ $achievements->url($i) }}" class="{{ $achievements->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($achievements->currentPage() < $achievements->lastPage())
            <a href="{{ $achievements->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $achievements->url($achievements->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection
