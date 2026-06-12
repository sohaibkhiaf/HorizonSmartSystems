@extends('admin.layouts.app-admin')

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

    @forelse($public_services as $service)
        <div class="achievement-card">

            <div class="achievement-card-image">
                @if($service->image)
                    <img src="{{ asset('uploads/'.$service->image) }}" alt="{{ $service->title }}">
                @else
                    <div class="image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
            </div>

            <div class="achievement-card-content">
                <div class="card-row">
                    <h3>{{ $service->fr_name }}</h3>

                    @if($service->is_published)
                        <span class="badge success">Publié</span>
                    @else
                        <span class="badge muted">Masqué</span>
                    @endif
                </div>

                <p>{{ $service->fr_short_description }}</p>

                <div class="card-actions">
                    <a href="{{ route('admin.public_services.edit', $service) }}" class="action-btn edit">
                        <i class="fas fa-pen"></i>
                        Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.public_services.destroy', $service) }}" onsubmit="return confirm('Supprimer cette service ?')">
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
    @if ($public_services->currentPage() > 1)
        <a href="{{$public_services->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @else
        <a href="{{$public_services->url($public_services->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
    @endif

    @for ($i = 1; $i <= $public_services->lastPage(); $i++)
        @if ($public_services->currentPage() == $i)
            <a href="{{$public_services->url($i)}}" class="active">{{$i}}</a>
        @else
            <a href="{{$public_services->url($i)}}">{{$i}}</a>
        @endif
    @endfor

    @if ($public_services->currentPage() < $public_services->lastPage())
        <a href="{{$public_services->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @else
        <a href="{{$public_services->url($public_services->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
    @endif
</div>

@endsection

