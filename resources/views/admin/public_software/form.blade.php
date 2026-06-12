<div class="container">
    <form method="POST" action="{{ isset($software) ? route('admin.publicSoftware.update', $software) : route('admin.publicSoftware.store') }}" 
        enctype="multipart/form-data">
        
        @csrf
        @if(isset($software)) @method('PUT') @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Nom Arabe</label>
                <input 
                    type="text" 
                    name="ar_name" 
                    value="{{ old('title', $software->ar_name ?? '') }}" 
                    placeholder="Entrez le nom de service en arabe"
                    required>
            </div>

            <div class="form-group">
                <label>Nom Francais</label>
                <input 
                    type="text" 
                    name="fr_name" 
                    value="{{ old('title', $software->fr_name ?? '') }}" 
                    placeholder="Entrez le nom de service en francais"
                    required>
            </div>
            <div class="form-group">
                <label>Nom Anglais</label>
                <input 
                    type="text" 
                    name="en_name" 
                    value="{{ old('title', $software->en_name ?? '') }}" 
                    placeholder="Entrez le nom de service en anglais"
                    required>
            </div>

            <div class="form-group full">
                <label>Description courte en arabe</label>
                <input 
                    type="text" 
                    name="ar_short_description" 
                    maxlength="300" 
                    value="{{ old('short_description', $software->ar_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>
            <div class="form-group full">
                <label>Description courte en francais</label>
                <input 
                    type="text" 
                    name="fr_short_description" 
                    maxlength="300" 
                    value="{{ old('short_description', $software->fr_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>
            <div class="form-group full">
                <label>Description courte en anglais</label>
                <input 
                    type="text" 
                    name="en_short_description" 
                    maxlength="300" 
                    value="{{ old('short_description', $software->en_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>

            <div class="form-group full">
                <label>Description arabe</label>
                <textarea 
                    name="ar_description" 
                    rows="5"
                    placeholder="Rédigez un service détaillée..."
                >{{ old('description', $software->ar_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description francaise</label>
                <textarea 
                    name="fr_description" 
                    rows="5"
                    placeholder="Rédigez un service détaillée..."
                >{{ old('description', $software->fr_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description anglaise</label>
                <textarea 
                    name="en_description" 
                    rows="5"
                    placeholder="Rédigez un service détaillée..."
                >{{ old('description', $software->en_description ?? '') }}</textarea>
            </div>


            <div class="form-group full">
                <label>Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*">
                <small>Télécharger une image du projet (JPG, PNG, etc.)</small>

                @if(!empty($software?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $software->image) }}" alt="">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label>Icon</label>
                <input 
                    type="file" 
                    name="icon" 
                    accept="image/*">
                <small>Télécharger une icon du projet (JPG, PNG, etc.)</small>

                @if(!empty($software?->icon))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $software->icon) }}" alt="">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label class="checkbox-row">
                    <input 
                        type="checkbox" 
                        name="is_published" 
                        value="1"
                        {{ old('is_published', $software->is_published ?? true) ? 'checked' : '' }}>
                    <span>Publié (visible sur le site)</span>
                </label>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.publicSoftware.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>


