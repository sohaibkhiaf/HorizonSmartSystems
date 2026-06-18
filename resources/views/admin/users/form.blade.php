<div class="container">

    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf

        @if(isset($user))
            @method('PUT')
        @endif

        @php
            $isEditing = isset($user);
            $isSelfAdmin = $isEditing && auth()->id() === $user->id && $user->role === 'admin';
        @endphp

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

                @if($isSelfAdmin)
                    <input type="text" value="Administrateur" readonly>

                    <input type="hidden" name="role" value="admin">

                    <small class="field-help">
                        Vous pouvez modifier vos informations, mais pas votre rôle.
                    </small>
                @else
                    <select name="role" required>
                        <option value="" disabled {{ !isset($user) ? 'selected' : '' }}>
                            Sélectionner un rôle
                        </option>

                        @if(auth()->user()->role === 'admin')
                            <option value="admin"
                                {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>
                                Administrateur
                            </option>
                        @endif

                        <option value="operator"
                            {{ old('role', $user->role ?? '') === 'operator' ? 'selected' : '' }}>
                            Opérateur
                        </option>

                        <option value="customer"
                            {{ old('role', $user->role ?? '') === 'customer' ? 'selected' : '' }}>
                            Client
                        </option>

                        <option value="user"
                            {{ old('role', $user->role ?? '') === 'user' ? 'selected' : '' }}>
                            Utilisateur
                        </option>
                    </select>

                    @if(auth()->user()->role === 'operator')
                        <small class="field-help">
                            Les opérateurs ne peuvent pas créer ou attribuer le rôle Administrateur.
                        </small>
                    @endif
                @endif
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
