<div class="container">
    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="name" value="{{ $user->name ?? '' }}" placeholder="Entrez le nom complet" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email ?? '' }}" placeholder="exemple@domaine.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="{{ isset($user) ? 'Laisser vide pour ne pas modifier' : 'Entrez un mot de passe' }}">
            </div>

            <div class="form-group">
                <label>Rôle</label>
                <select name="role" required>
                    <option value="" disabled {{ !isset($user) ? 'selected' : '' }}>Sélectionner un rôle</option>
                    <option value="USER" {{ ($user->role ?? '') == 'USER' ? 'selected' : '' }}>USER</option>
                    <option value="ADMIN" {{ ($user->role ?? '') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="OPERATOR" {{ ($user->role ?? '') == 'OPERATOR' ? 'selected' : '' }}>OPERATOR</option>
                    <option value="CLIENT" {{ ($user->role ?? '') == 'CLIENT' ? 'selected' : '' }}>CLIENT</option>
                </select>
            </div>

        </div>

        <div class="form-actions">
            <button class="btn-main">
                Enregistrer
            </button>

            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>
