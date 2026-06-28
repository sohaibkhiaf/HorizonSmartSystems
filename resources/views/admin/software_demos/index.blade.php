@extends('admin.layouts.admin')

@section('title', 'Démos logiciels')
@section('page_title', 'Démos logiciels')
@section('page_subtitle', 'Gérer les démos liées aux logiciels publics')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/software_demos/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.software_demos.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer une démo
    </a>
</div>

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Logiciel</th>
                <th>Démo</th>
                <th>Lien</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($demos as $demo)
                <tr>
                    <td>#{{ $demo->id }}</td>

                    <td>
                        <div class="demo-thumb">
                            @if($demo->image)
                                <img src="{{ asset('uploads/' . $demo->image) }}" alt="{{ $demo->name }}">
                            @else
                                <div class="thumb-placeholder">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        @if($demo->publicSoftware)
                            <div class="software-info">
                                <strong>{{ $demo->publicSoftware->fr_name }}</strong>

                                @if($demo->publicSoftware->icon)
                                    <img
                                        src="{{ asset('uploads/' . $demo->publicSoftware->icon) }}"
                                        alt="Icône {{ $demo->publicSoftware->fr_name }}">
                                @endif
                            </div>
                        @else
                            <span class="badge warning">
                                <i class="fas fa-triangle-exclamation"></i>
                                Logiciel manquant
                            </span>
                        @endif
                    </td>

                    <td>
                        <strong>{{ $demo->name }}</strong>
                    </td>

                    <td>
                        @if($demo->demo_link)
                            <a href="{{ $demo->demo_link }}" target="_blank" class="link-btn">
                                <i class="fas fa-up-right-from-square"></i>
                                Ouvrir
                            </a>
                        @else
                            <span class="badge muted">
                                Aucun lien
                            </span>
                        @endif
                    </td>

                    <td>
                        @if($demo->is_active)
                            <span class="badge success">
                                <i class="fas fa-eye"></i>
                                Active
                            </span>
                        @else
                            <span class="badge muted">
                                <i class="fas fa-eye-slash"></i>
                                Masquée
                            </span>
                        @endif
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.software_demos.edit', $demo) }}" class="action-btn edit">
                                <i class="fas fa-pen"></i>
                                Modifier
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.software_demos.destroy', $demo) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cette démo ?')">
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
                        Aucune démo trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@if($demos->hasPages())
    <div class="navigator-container">
        @if ($demos->currentPage() > 1)
            <a href="{{ $demos->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $demos->url($demos->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $demos->lastPage(); $i++)
            <a href="{{ $demos->url($i) }}" class="{{ $demos->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($demos->currentPage() < $demos->lastPage())
            <a href="{{ $demos->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $demos->url($demos->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection

