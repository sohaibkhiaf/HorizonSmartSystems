@extends('admin.layouts.admin')

@section('page_title', 'Journal d\'activité')
@section('page_subtitle', 'Historique des actions dans le panneau admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/logs/index.css') }}">
@endsection

@section('content')

<div class="filter-container">
    <form method="GET" action="{{ route('admin.logs.index') }}" class="filter-form">

        <div class="filter-group">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Rechercher par utilisateur, action, modèle...">
        </div>

        <div class="filter-group">
            <select name="action">
                <option value="">Toutes les actions</option>
                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>
                    Création
                </option>
                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>
                    Modification
                </option>
                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>
                    Suppression
                </option>
            </select>
        </div>

        <div class="filter-group">
            <input
                type="text"
                name="model_type"
                value="{{ request('model_type') }}"
                placeholder="Type du modèle">
        </div>

        <div class="filter-group">
            <input
                type="date"
                name="date_from"
                value="{{ request('date_from') }}">
        </div>

        <div class="filter-group">
            <input
                type="date"
                name="date_to"
                value="{{ request('date_to') }}">
        </div>

        <div class="filter-actions">
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i>
                Filtrer
            </button>

            <a href="{{ route('admin.logs.index') }}" class="reset-btn">
                <i class="fas fa-rotate-left"></i>
                Réinitialiser
            </a>
        </div>

    </form>
</div>

<div class="danger-container">
    <form method="POST"
          action="{{ route('admin.logs.destroy') }}"
          class="delete-form">
        @csrf
        @method('DELETE')

        <div>
            <h3>Nettoyer les anciens logs</h3>
            <p>Supprimer tous les logs créés avant la date sélectionnée.</p>
        </div>

        <div class="delete-actions">
            <input type="date" name="delete_before_date" required>

            <button type="submit"
                    class="delete-btn"
                    onclick="return confirm('Supprimer définitivement les logs avant cette date ?')">
                <i class="fas fa-trash"></i>
                Supprimer
            </button>
        </div>
    </form>
</div>

<div class="container">

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Modèle</th>
                <th>ID modèle</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>#{{ $log->id }}</td>

                    <td>
                        @if($log->user)
                            <strong>{{ $log->user->name }}</strong><br>
                            <span>{{ $log->user->username }}</span>
                        @else
                            <span>Utilisateur supprimé</span>
                        @endif
                    </td>

                    <td>
                        @if($log->action == 'created')
                            <span class="badge success">Création</span>
                        @elseif($log->action == 'updated')
                            <span class="badge warning">Modification</span>
                        @elseif($log->action == 'deleted')
                            <span class="badge danger">Suppression</span>
                        @else
                            <span class="badge neutral">{{ $log->action }}</span>
                        @endif
                    </td>

                    <td>{{ class_basename($log->model_type) }}</td>

                    <td>#{{ $log->model_id }}</td>

                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">
                        Aucun log trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@if ($logs->hasPages())
    <div class="navigator-container">
        @if ($logs->currentPage() > 1)
            <a href="{{ $logs->previousPageUrl() }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @else
            <a href="{{ $logs->url($logs->currentPage()) }}">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        @endif

        @for ($i = 1; $i <= $logs->lastPage(); $i++)
            <a href="{{ $logs->url($i) }}"
               class="{{ $logs->currentPage() == $i ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($logs->currentPage() < $logs->lastPage())
            <a href="{{ $logs->nextPageUrl() }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @else
            <a href="{{ $logs->url($logs->currentPage()) }}">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        @endif
    </div>
@endif

@endsection
