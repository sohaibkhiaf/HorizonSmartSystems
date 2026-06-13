@extends('admin.layouts.admin')

@section('title', 'Nos logiciels')
@section('page_title', 'Nos logiciels')
@section('page_subtitle', 'Gérer tous les logiciels du site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_software/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.public_software.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer un logiciel
    </a>
</div>

<div class="software-grid">

    @forelse($public_software as $software)
        <div class="software-card">

            <div class="software-image">
                @if($software->image)
                    <img src="{{ asset('uploads/' . $software->image) }}" alt="{{ $software->fr_name }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                @endif

                <div class="software-icon">
                    @if($software->icon)
                        <img src="{{ asset('uploads/' . $software->icon) }}" alt="Icône {{ $software->fr_name }}">
                    @else
                        <i class="fas fa-code"></i>
                    @endif
                </div>
            </div>

            <div class="software-content">

                <div class="card-top">
                    <div>
                        <h3>{{ $software->fr_name }}</h3>

                        <div class="card-badges">
                            @if($software->is_published)
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
                                Logiciel
                            </span>
                        </div>
                    </div>
                </div>

                <p class="description">
                    {{ Str::limit($software->fr_short_description, 140) }}
                </p>


                <div class="meta-grid">
                    <div>
                        <span>Image</span>
                        <strong>{{ $software->image ? 'Disponible' : 'Manquante' }}</strong>
                    </div>

                    <div>
                        <span>Icône</span>
                        <strong>{{ $software->icon ? 'Disponible' : 'Manquante' }}</strong>
                    </div>

                    <div>
                        <span>Créé le</span>
                        <strong>{{ $software->created_at?->format('Y-m-d') }}</strong>
                    </div>

                    <div>
                        <span>Mis à jour</span>
                        <strong>{{ $software->updated_at?->format('Y-m-d') }}</strong>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="{{ route('admin.public_software.edit', $software) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.public_software.destroy', $software) }}" onsubmit="return confirm('Supprimer ce logiciel ?')">
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
                <i class="fas fa-laptop-code"></i>
            </div>

            <h3>Aucun logiciel trouvé</h3>
            <p>Vous n’avez pas encore ajouté de logiciel sur le site.</p>

            <a href="{{ route('admin.public_software.create') }}" class="empty-link">
                <i class="fas fa-plus"></i>
                Créer un logiciel
            </a>
        </div>
    @endforelse

</div>

@if($public_software->hasPages())
    <div class="navigator-container">
        @if ($public_software->currentPage() > 1)
            <a href="{{ $public_software->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $public_software->url($public_software->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $public_software->lastPage(); $i++)
            <a href="{{ $public_software->url($i) }}" class="{{ $public_software->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($public_software->currentPage() < $public_software->lastPage())
            <a href="{{ $public_software->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $public_software->url($public_software->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection
