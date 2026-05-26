@extends('admin.layouts.app')

@section('title', 'Nos réalisations')
@section('page_title', 'Nos réalisations')
@section('page_subtitle', 'Gérer tous les projets du site')

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.achievements.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer une réalisation
    </a>
</div>

<div class="cards-grid">

    @forelse($achievements as $achievement)
        <div class="admin-card">

            <div class="admin-card-image">
                @if($achievement->image)
                    <img src="{{ asset('uploads/' . $achievement->image) }}" alt="{{ $achievement->title }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>

            <div class="admin-card-content">
                <div class="card-row">
                    <h3>{{ $achievement->title }}</h3>

                    @if($achievement->is_published)
                        <span class="badge success">Publié</span>
                    @else
                        <span class="badge muted">Masqué</span>
                    @endif
                </div>

                @if($achievement->type)
                    <span class="type-badge">{{ $achievement->type }}</span>
                @endif

                <p>{{ $achievement->short_description }}</p>

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
        <div class="empty-box">
            Aucune réalisation trouvée.
        </div>
    @endforelse

</div>

<div class="pagination-box">
    {{ $achievements->links() }}
</div>

@endsection

