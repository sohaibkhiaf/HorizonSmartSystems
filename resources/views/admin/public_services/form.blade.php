<div class="container">
    <form method="POST" action="{{ isset($service) ? route('admin.public_services.update', $service) : route('admin.public_services.store') }}" 
        enctype="multipart/form-data">
        
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Nom Arabe</label>
                <input 
                    type="text" 
                    name="ar_name" 
                    value="{{ old('title', $service->ar_name ?? '') }}" 
                    placeholder="Entrez le nom de service en arabe"
                    required>
            </div>

            <div class="form-group">
                <label>Nom Francais</label>
                <input 
                    type="text" 
                    name="fr_name" 
                    value="{{ old('title', $service->fr_name ?? '') }}" 
                    placeholder="Entrez le nom de service en francais"
                    required>
            </div>
            <div class="form-group">
                <label>Nom Anglais</label>
                <input 
                    type="text" 
                    name="en_name" 
                    value="{{ old('title', $service->en_name ?? '') }}" 
                    placeholder="Entrez le nom de service en anglais"
                    required>
            </div>

            <div class="form-group full">
                <label>Description courte en arabe</label>
                <input 
                    type="text" 
                    name="ar_short_description" 
                    maxlength="300" 
                    value="{{ old('short_description', $service->ar_short_description ?? '') }}" 
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
                    value="{{ old('short_description', $service->fr_short_description ?? '') }}" 
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
                    value="{{ old('short_description', $service->en_short_description ?? '') }}" 
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
                >{{ old('description', $service->ar_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description francaise</label>
                <textarea 
                    name="fr_description" 
                    rows="5"
                    placeholder="Rédigez un service détaillée..."
                >{{ old('description', $service->fr_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description anglaise</label>
                <textarea 
                    name="en_description" 
                    rows="5"
                    placeholder="Rédigez un service détaillée..."
                >{{ old('description', $service->en_description ?? '') }}</textarea>
            </div>


            <div class="form-group full">
                <label>Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*">
                <small>Télécharger une image du projet (JPG, PNG, etc.)</small>

                @if(!empty($service?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $service->image) }}" alt="">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label>Type</label>
                <select name="type" >
                    <option value="">-- Séléctionner --</option>
                    <option value="main">Principale</option>
                    <option value="other">Autre</option>
                </select>
            </div>


            <div class="form-group full">
                <label class="checkbox-row">
                    <input 
                        type="checkbox" 
                        name="is_published" 
                        value="1"
                        {{ old('is_published', $service->is_published ?? true) ? 'checked' : '' }}>
                    <span>Publié (visible sur le site)</span>
                </label>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.public_services.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>


