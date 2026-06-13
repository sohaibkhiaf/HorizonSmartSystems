@extends('admin.layouts.admin')

@section('title', 'Nos réalisations')
@section('page_title', 'Nos réalisations')
@section('page_subtitle', 'Gérer tous les projets du site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/achievements/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.achievements.create') }}" class="btn-main">
        <i class="fas fa-plus"></i>
        Créer une réalisation
    </a>
</div>

<div class="container">

    @forelse($achievements as $achievement)
        <div class="achievement-card">

            <div class="achievement-card-image">
                @if($achievement->image)
                    <img src="{{ asset('uploads/' . $achievement->image) }}" alt="{{ $achievement->title }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>

            <div class="achievement-card-content">
                <div class="card-row">
                    <h3>{{ $achievement->fr_title }}</h3>

                    @if($achievement->is_published)
                        <span class="badge success">Publié</span>
                    @else
                        <span class="badge muted">Masqué</span>
                    @endif
                </div>

                @if($achievement->type)
                    <span class="type-badge">{{ $achievement->fr_type }}</span>
                @endif

                <p>{{ $achievement->fr_short_description }}</p>

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

<div class="navigator-container">
    @if ($achievements->currentPage() > 1)
        <a href="{{$achievements->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @else
        <a href="{{$achievements->url($achievements->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @endif

    @for ($i = 1; $i <= $achievements->lastPage(); $i++)
        @if ($achievements->currentPage() == $i)
            <a href="{{$achievements->url($i)}}" class="active">{{$i}}</a>
        @else
            <a href="{{$achievements->url($i)}}">{{$i}}</a>
        @endif
    @endfor

    @if ($achievements->currentPage() < $achievements->lastPage())
        <a href="{{$achievements->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @else
        <a href="{{$achievements->url($achievements->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @endif
</div>

@endsection

