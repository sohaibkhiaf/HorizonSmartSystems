<div class="container">

    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>
                    Nom complet
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name ?? '') }}"
                    placeholder="Ex: Mohamed Benali"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Adresse email
                    <span class="required">*</span>
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    placeholder="exemple@domaine.com"
                    required>
            </div>

            <div class="form-group">
                <label>Numéro de téléphone</label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone ?? '') }}"
                    placeholder="Ex: 0550 12 34 56">
            </div>

            <div class="form-group">
                <label>Entreprise</label>

                <input
                    type="text"
                    name="company"
                    value="{{ old('company', $user->company ?? '') }}"
                    placeholder="Nom de l'entreprise">
            </div>

            <div class="form-group">
                <label>
                    Mot de passe
                    @if(!isset($user))
                        <span class="required">*</span>
                    @endif
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="{{ isset($user) ? 'Laisser vide pour conserver le mot de passe actuel' : 'Minimum 6 caractères' }}"
                    {{ !isset($user) ? 'required' : '' }}>

                @if(isset($user))
                    <small class="field-help">
                        Facultatif lors de la modification.
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label>
                    Rôle
                    <span class="required">*</span>
                </label>

                <select name="role" required>
                    <option value="" disabled {{ !isset($user) ? 'selected' : '' }}>
                        Sélectionner un rôle
                    </option>

                    <option value="ADMIN" {{ ($user->role ?? '') == 'ADMIN' ? 'selected' : '' }}>
                        Administrateur
                    </option>

                    <option value="OPERATOR" {{ ($user->role ?? '') == 'OPERATOR' ? 'selected' : '' }}>
                        Opérateur
                    </option>

                    <option value="CUSTOMER" {{ ($user->role ?? '') == 'CUSTOMER' ? 'selected' : '' }}>
                        Client
                    </option>

                    <option value="USER" {{ ($user->role ?? '') == 'USER' ? 'selected' : '' }}>
                        Utilisateur
                    </option>
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
