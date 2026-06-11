<div class="container">
    <form method="POST" action="{{ isset($achievement) ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}" 
        enctype="multipart/form-data">
        
        @csrf
        @if(isset($achievement)) @method('PUT') @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Titre</label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title', $achievement->title ?? '') }}" 
                    placeholder="Entrez le titre du projet (ex. Site d'entreprise)"
                    required
                >
            </div>

            <div class="form-group">
                <label>Type</label>
                <input type="text" 
                    name="type" 
                    value="{{ old('title', $achievement->type ?? '') }}" 
                    placeholder="Entrez le type de projet (ex. E-commerce)" 
                    required/>
            </div>

            <div class="form-group full">
                <label>Description courte</label>
                <input 
                    type="text" 
                    name="short_description" 
                    maxlength="300" 
                    value="{{ old('short_description', $achievement->short_description ?? '') }}" 
                    placeholder="Résumé bref (300 caractères max)"
                    required
                >
            </div>

            <div class="form-group full">
                <label>Description</label>
                <textarea 
                    name="description" 
                    rows="5"
                    placeholder="Rédigez une description détaillée du projet..."
                >{{ old('description', $achievement->description ?? '') }}</textarea>
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


