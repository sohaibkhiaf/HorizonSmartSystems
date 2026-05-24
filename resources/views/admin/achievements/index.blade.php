@extends('admin.layouts.app')

@section('title', 'Our Achievements')
@section('page_title', 'Our Achievements')
@section('page_subtitle', 'Manage all website projects')

@section('content')

<div class="page-actions">
    <a href="{{ route('admin.achievements.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Create Achievement 
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
                        <span class="badge success">Published</span>
                    @else
                        <span class="badge muted">Hidden</span>
                    @endif
                </div>

                @if($achievement->type)
                    <span class="type-badge">{{ $achievement->type }}</span>
                @endif

                <p>{{ $achievement->short_description }}</p>

                <div class="card-actions">
                    <a href="{{ route('admin.achievements.edit', $achievement) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.achievements.destroy', $achievement) }}" onsubmit="return confirm('Delete this achievement?')">
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
            No achievements found.
        </div>
    @endforelse

</div>

<div class="pagination-box">
    {{ $achievements->links() }}
</div>

@endsection

