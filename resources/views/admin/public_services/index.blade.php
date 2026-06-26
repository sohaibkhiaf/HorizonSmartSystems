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

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Service</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($public_services as $service)
                <tr>
                    <td>#{{ $service->id }}</td>

                    <td>
                        <div class="service-thumb">
                            @if($service->image)
                                <img src="{{ asset('uploads/' . $service->image) }}" alt="{{ $service->fr_name }}">
                            @else
                                <div class="thumb-placeholder">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        <strong>{{ $service->fr_name }}</strong>
                    </td>

                    <td>
                        @if($service->type === 'main')
                            <span class="badge type">Service principal</span>
                        @else
                            <span class="badge muted">Service secondaire</span>
                        @endif
                    </td>

                    <td>
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
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.public_services.edit', $service) }}" class="action-btn edit">
                                <i class="fas fa-pen"></i>
                                Modifier
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.public_services.destroy', $service) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer ce service ?')">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        Aucun service trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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

