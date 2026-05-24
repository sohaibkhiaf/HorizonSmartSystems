@extends('admin.layouts.app')

@section('title', 'Our Realisations')
@section('page_title', 'Our Realisations')
@section('page_subtitle', 'Manage all website projects')

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.realisations.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Create Realisation
    </a>
</div>

<div class="cards-grid">

    @forelse($realisations as $realisation)
        <div class="admin-card">

            <div class="admin-card-image">
                @if($realisation->image)
                    <img src="{{ asset('storage/' . $realisation->image) }}" alt="{{ $realisation->title }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>

            <div class="admin-card-content">
                <div class="card-row">
                    <h3>{{ $realisation->title }}</h3>

                    @if($realisation->is_published)
                        <span class="badge success">Published</span>
                    @else
                        <span class="badge muted">Hidden</span>
                    @endif
                </div>

                @if($realisation->type)
                    <span class="type-badge">{{ $realisation->type }}</span>
                @endif

                <p>{{ $realisation->short_description }}</p>

                <div class="card-actions">
                    <a href="{{ route('admin.realisations.edit', $realisation) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.realisations.destroy', $realisation) }}" onsubmit="return confirm('Delete this realisation?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="action-btn delete">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>

        </div>
    @empty
        <div class="empty-box">
            No realisations found.
        </div>
    @endforelse

</div>

<div class="pagination-box">
    {{ $realisations->links() }}
</div>

@endsection

