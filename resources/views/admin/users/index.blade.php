@extends('admin.layouts.app-admin')

@section('title', 'Utilisateurs')
@section('page_title', 'Utilisateurs')
@section('page_subtitle', 'Gérer tous les utilisateurs')

@section('content')

<div class="page-header">
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Rechercher nom ou email..." value="{{ request('search') }}">
        <button class="btn-secondary">Rechercher</button>
    </form>

    <a href="{{ route('admin.users.create') }}" class="btn-main">
        <i class="fas fa-user-plus"></i>
        Ajouter
    </a>
</div>

<div class="content-card table-card">

    @if($users->count())
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="role-badge role-{{ strtolower($user->role) }}">
                        {{ $user->role }}
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit btn-sm">
                            <i class="fas fa-pen"></i>
                        </a>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button class="action-btn delete btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-box">
        {{ $users->links() }}
    </div>

    @else
        <div class="empty-users">
            Aucun utilisateur trouvé
        </div>
    @endif

    <div class="navigator-container">
        @if ($users->currentPage() > 1)
            <a href="{{$users->previousPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @else
            <a href="{{$users->url($users->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left'}}"></i></a>
        @endif

        @for ($i = 1; $i <= $users->lastPage(); $i++)
            @if ($users->currentPage() == $i)
                <a href="{{$users->url($i)}}" class="active">{{$i}}</a>
            @else
                <a href="{{$users->url($i)}}">{{$i}}</a>
            @endif
        @endfor

        @if ($users->currentPage() < $users->lastPage())
            <a href="{{$users->nextPageUrl()}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @else
            <a href="{{$users->url($users->currentPage())}}"><i class="{{(app()->getLocale() === 'ar') ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right'}}"></i></a>
        @endif
    </div>

</div>

@endsection

