@extends('admin.layouts.admin')

@section('title', 'Nos services')
@section('page_title', 'Nos services')
@section('page_subtitle', 'Gérer tous les services du site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_services/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.public_services.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer un service
    </a>
</div>

<div class="services-grid">

    @forelse($public_services as $service)
        <div class="service-card">

            <div class="service-image">
                @if($service->image)
                    <img src="{{ asset('uploads/' . $service->image) }}" alt="{{ $service->fr_name }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-layer-group"></i>
                    </div>
                @endif
            </div>

            <div class="service-content">

                <div class="card-top">
                    <div>
                        <h3>{{ $service->fr_name }}</h3>

                        <div class="card-badges">
                            @if($service->is_published)
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
                                @if($service->type === 'main')
                                    Service principal
                                @else
                                    Service secondaire
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <p class="description">
                    {{ Str::limit($service->fr_short_description, 140) }}
                </p>

                <div class="card-actions">
                    <a href="{{ route('admin.public_services.edit', $service) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.public_services.destroy', $service) }}" onsubmit="return confirm('Supprimer ce service ?')">
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
                <i class="fas fa-layer-group"></i>
            </div>

            <h3>Aucun service trouvé</h3>
            <p>Vous n’avez pas encore ajouté de service sur le site.</p>

            <a href="{{ route('admin.public_services.create') }}" class="empty-link">
                <i class="fas fa-plus"></i>
                Créer un service
            </a>
        </div>
    @endforelse

</div>

@if($public_services->hasPages())
    <div class="navigator-container">
        @if ($public_services->currentPage() > 1)
            <a href="{{ $public_services->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $public_services->url($public_services->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $public_services->lastPage(); $i++)
            <a href="{{ $public_services->url($i) }}" class="{{ $public_services->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($public_services->currentPage() < $public_services->lastPage())
            <a href="{{ $public_services->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $public_services->url($public_services->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection