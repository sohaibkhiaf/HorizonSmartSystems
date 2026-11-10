<div class="container">

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ isset($demo) ? route('admin.software_demos.update', $demo) : route('admin.software_demos.store') }}"
        enctype="multipart/form-data">

        @csrf

        @if(isset($demo))
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group full">
                <label>
                    Logiciel
                    <span class="required">*</span>
                </label>

                <select name="public_software_id" required>
                    <option value="">Sélectionner un logiciel</option>

                    @foreach($publicSoftware as $software)
                        <option
                            value="{{ $software->id }}"
                            {{ (string) old('public_software_id', $demo->public_software_id ?? '') === (string) $software->id ? 'selected' : '' }}>
                            {{ $software->fr_name }}
                        </option>
                    @endforeach
                </select>

                <small class="field-help">
                    Cette démo sera affichée avec ce logiciel dans le portail client.
                </small>
            </div>

            <div class="form-group full">
                <label>
                    Nom de la démo
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $demo->name ?? '') }}"
                    placeholder="Ex: Démo de gestion de stock"
                    required>
            </div>

            <div class="form-group full">
                <label>Lien de la démo</label>

                <input
                    type="url"
                    name="demo_link"
                    value="{{ old('demo_link', $demo->demo_link ?? '') }}"
                    placeholder="https://example.com/demo">

                <small class="field-help">
                    Laissez vide si la démo n’a pas encore de lien.
                </small>
            </div>

            <div class="form-group full">
                <label>Image de la démo</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*">

                <small class="field-help">
                    Image affichée dans le slider du portail client. Formats recommandés : JPG, PNG ou WEBP.
                </small>

                @if(!empty($demo?->image))
                    <div class="current-image">
                        <span>Image actuelle</span>
                        <img src="{{ asset('uploads/' . $demo->image) }}" alt="Image de la démo">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label>Note admin</label>

                <textarea
                    name="admin_note"
                    rows="5"
                    placeholder="Note interne visible uniquement par l’administration...">{{ old('admin_note', $demo->admin_note ?? '') }}</textarea>
            </div>

            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $demo->is_active ?? true) ? 'checked' : '' }}>
                Afficher cette démo dans le portail client
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.software_demos.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>
