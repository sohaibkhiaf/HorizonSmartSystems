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

    @forelse($public_software as $software)
        <div class="achievement-card">

            <div class="achievement-card-image">
                @if($software->image)
                    <img src="{{ asset('uploads/'.$software->image) }}" alt="{{ $software->fr_name }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>

            <div class="achievement-card-content">
                <div class="card-row">
                    <h3>{{ $software->fr_name }}</h3>

                    @if($software->is_published)
                        <span class="badge success">Publié</span>
                    @else
                        <span class="badge muted">Masqué</span>
                    @endif
                </div>

                <p>{{ $software->fr_short_description }}</p>

                <div class="card-actions">
                    <a href="{{ route('admin.public_software.edit', $software) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.public_software.destroy', $software) }}" onsubmit="return confirm('Supprimer cette service ?')">
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
            Aucun service trouvé.
        </div>
    @endforelse

</div>

<div class="navigator-container">
    @if ($public_software->currentPage() > 1)
        <a href="{{$public_software->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @else
        <a href="{{$public_software->url($public_software->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @endif

    @for ($i = 1; $i <= $public_software->lastPage(); $i++)
        @if ($public_software->currentPage() == $i)
            <a href="{{$public_software->url($i)}}" class="active">{{$i}}</a>
        @else
            <a href="{{$public_software->url($i)}}">{{$i}}</a>
        @endif
    @endfor

    @if ($public_software->currentPage() < $public_software->lastPage())
        <a href="{{$public_software->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @else
        <a href="{{$public_software->url($public_software->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @endif
</div>

@endsection

