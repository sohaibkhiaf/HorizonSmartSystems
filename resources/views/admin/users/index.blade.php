@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')
@section('page_title', 'Utilisateurs')
@section('page_subtitle', 'Gérer tous les utilisateurs')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/users/index.css') }}">
@endsection

@section('content')

<div class="header">
    <a href="{{ route('admin.users.create') }}" class="header-btn">
        <i class="fas fa-user-plus"></i>
        Ajouter
    </a>
</div>

<div class="filter">
    <form method="GET" class="filter-form">

        <input
            type="text"
            name="search"
            placeholder="Rechercher nom ou username..."
            value="{{ request('search') }}">

        <select name="role">
            <option value="">Tous les rôles</option>

            @foreach($roles as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>
                    @if($role === 'admin')
                        Administrateur
                    @elseif($role === 'operator')
                        Opérateur
                    @elseif($role === 'customer')
                        Client
                    @else
                        Utilisateur
                    @endif
                </option>
            @endforeach
        </select>

        <div class="filter-actions">
            <button class="btn-search">
                <i class="fas fa-search"></i>
                Rechercher
            </button>

            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-rotate-right"></i>
                Réinitialiser
            </a>
        </div>

    </form>

</div>

<div class="container">

    @if($users->count())
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Username</th>
                        <th>Entreprise</th>
                        <th>Contact</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>

                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>

                            <td>{{ $user->username }}</td>

                            <td>
                                {{ $user->company ?: '—' }}
                            </td>

                            <td>
                                {{ $user->phone ?: '—' }}
                            </td>

                            <td>
                                <span class="role-badge role-{{ strtolower($user->role) }}">
                                    @if($user->role === 'admin')
                                        Administrateur
                                    @elseif($user->role === 'operator')
                                        Opérateur
                                    @elseif($user->role === 'customer')
                                        Client
                                    @else
                                        Utilisateur
                                    @endif
                                </span>
                            </td>

                            <td>
                                <div class="table-actions">

                                    {{-- Edit --}}
                                    @if($user->role !== 'admin' || auth()->id() === $user->id)
                                        <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit">
                                            <i class="fas fa-pen"></i>
                                            Modifier
                                        </a>
                                    @endif

                                    {{-- Delete : admin only, cannot delete self, cannot delete another admin --}}
                                    @if(
                                        auth()->user()->role === 'admin'
                                        && $user->role !== 'admin'
                                        && auth()->id() !== $user->id
                                    )
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="action-btn delete"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <i class="fas fa-trash"></i>
                                                Supprimer
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty">
            Aucun utilisateur trouvé
        </div>
    @endif

    <div class="navigator-container">
        @if ($users->currentPage() > 1)
            <a href="{{ $users->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $users->url($users->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $users->lastPage(); $i++)
            <a href="{{ $users->url($i) }}" class="{{ $users->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($users->currentPage() < $users->lastPage())
            <a href="{{ $users->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $users->url($users->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>

</div>

@endsection
