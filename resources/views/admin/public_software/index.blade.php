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

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Icône</th>
                <th>Logiciel</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($public_software as $software)
                <tr>
                    <td>#{{ $software->id }}</td>

                    <td>
                        <div class="software-thumb">
                            @if($software->image)
                                <img src="{{ asset('uploads/' . $software->image) }}" alt="{{ $software->fr_name }}">
                            @else
                                <div class="thumb-placeholder">
                                    <i class="fas fa-laptop-code"></i>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        <div class="software-icon-thumb">
                            @if($software->icon)
                                <img src="{{ asset('uploads/' . $software->icon) }}" alt="Icône {{ $software->fr_name }}">
                            @else
                                <i class="fas fa-code"></i>
                            @endif
                        </div>
                    </td>

                    <td>
                        <strong>{{ $software->fr_name }}</strong>
                    </td>

                    <td>
                        <span class="badge type">Logiciel</span>
                    </td>

                    <td>
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
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.public_software.edit', $software) }}" class="action-btn edit">
                                <i class="fas fa-pen"></i>
                                Modifier
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.public_software.destroy', $software) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer ce logiciel ?')">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">
                        Aucun logiciel trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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

