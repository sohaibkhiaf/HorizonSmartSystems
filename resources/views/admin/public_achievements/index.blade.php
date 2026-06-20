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

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Réalisation</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Lien</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($achievements as $achievement)
                <tr>
                    <td>#{{ $achievement->id }}</td>

                    <td>
                        <div class="achievement-thumb">
                            @if($achievement->image)
                                <img src="{{ asset('uploads/' . $achievement->image) }}" alt="{{ $achievement->fr_title }}">
                            @else
                                <div class="thumb-placeholder">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        <strong>{{ $achievement->fr_title }}</strong>
                    </td>

                    <td>
                        <span class="badge type">
                            {{ $achievement->fr_type }}
                        </span>
                    </td>

                    <td>
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
                    </td>

                    <td>
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
                    </td>

                    <td class="description-cell">
                        {{ Str::limit($achievement->fr_short_description, 80) }}
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.achievements.edit', $achievement) }}" class="action-btn edit">
                                <i class="fas fa-pen"></i>
                                Modifier
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.achievements.destroy', $achievement) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="action-btn delete"
                                        onclick="return confirm('Supprimer cette réalisation ?')">
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
                        Aucune réalisation trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

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

