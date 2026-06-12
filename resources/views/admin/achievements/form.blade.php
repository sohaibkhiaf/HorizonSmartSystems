<div class="container">
    <form method="POST" action="{{ isset($achievement) ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}" 
        enctype="multipart/form-data">
        
        @csrf
        @if(isset($achievement)) @method('PUT') @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Titre Arabe</label>
                <input 
                    type="text" 
                    name="ar_title" 
                    value="{{ old('ar_title', $achievement->ar_title ?? '') }}" 
                    placeholder="Entrez le titre du projet (ex. Site d'entreprise)"
                    required
                >
            </div>
            <div class="form-group">
                <label>Titre Francais</label>
                <input 
                    type="text" 
                    name="fr_title" 
                    value="{{ old('fr_title', $achievement->fr_title ?? '') }}" 
                    placeholder="Entrez le titre du projet (ex. Site d'entreprise)"
                    required
                >
            </div>
            <div class="form-group">
                <label>Titre Anglais</label>
                <input 
                    type="text" 
                    name="en_title" 
                    value="{{ old('en_title', $achievement->en_title ?? '') }}" 
                    placeholder="Entrez le titre du projet (ex. Site d'entreprise)"
                    required
                >
            </div>

            <div class="form-group">
                <label>Type Arabe</label>
                <input type="text" 
                    name="ar_type" 
                    value="{{ old('ar_type', $achievement->ar_type ?? '') }}" 
                    placeholder="Entrez le type de projet (ex. E-commerce)" 
                    required/>
            </div>
            <div class="form-group">
                <label>Type Francais</label>
                <input type="text" 
                    name="fr_type" 
                    value="{{ old('fr_type', $achievement->fr_type ?? '') }}" 
                    placeholder="Entrez le type de projet (ex. E-commerce)" 
                    required/>
            </div>
            <div class="form-group">
                <label>Type Anglais</label>
                <input type="text" 
                    name="en_type" 
                    value="{{ old('en_type', $achievement->en_type ?? '') }}" 
                    placeholder="Entrez le type de projet (ex. E-commerce)" 
                    required/>
            </div>

            <div class="form-group full">
                <label>Description courte Arabe</label>
                <input 
                    type="text" 
                    name="ar_short_description" 
                    maxlength="300" 
                    value="{{ old('ar_short_description', $achievement->ar_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>
            <div class="form-group full">
                <label>Description courte Francaise</label>
                <input 
                    type="text" 
                    name="fr_short_description" 
                    maxlength="300" 
                    value="{{ old('fr_short_description', $achievement->fr_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>
            <div class="form-group full">
                <label>Description courte anglaise</label>
                <input 
                    type="text" 
                    name="en_short_description" 
                    maxlength="300" 
                    value="{{ old('en_short_description', $achievement->en_short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>

            <div class="form-group full">
                <label>Description Arabe</label>
                <textarea 
                    name="ar_description" 
                    rows="5"
                    placeholder="Rédigez une description détaillée du projet..."
                >{{ old('ar_description', $achievement->ar_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description Francais</label>
                <textarea 
                    name="fr_description" 
                    rows="5"
                    placeholder="Rédigez une description détaillée du projet..."
                >{{ old('fr_description', $achievement->fr_description ?? '') }}</textarea>
            </div>
            <div class="form-group full">
                <label>Description Anglaise</label>
                <textarea 
                    name="en_description" 
                    rows="5"
                    placeholder="Rédigez une description détaillée du projet..."
                >{{ old('en_description', $achievement->en_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>URL du projet</label>
                <input 
                    type="url" 
                    name="project_url" 
                    value="{{ old('project_url', $achievement->project_url ?? '') }}" 
                    placeholder="https://exemple.com"
                >
            </div>

            <div class="form-group full">
                <label>Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*">
                <small>Télécharger une image du projet (JPG, PNG, etc.)</small>

                @if(!empty($achievement?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $achievement->image) }}" alt="">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label class="checkbox-row">
                    <input 
                        type="checkbox" 
                        name="is_published" 
                        value="1"
                        {{ old('is_published', $achievement->is_published ?? true) ? 'checked' : '' }}>
                    <span>Publié (visible sur le site)</span>
                </label>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.achievements.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>


