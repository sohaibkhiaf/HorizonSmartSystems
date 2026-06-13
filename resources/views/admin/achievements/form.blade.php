<div class="container">

    <div class="form-note">
        <span>*</span> Champs obligatoires
    </div>

    <form
        method="POST"
        action="{{ isset($achievement) ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}"
        enctype="multipart/form-data">

        @csrf
        @if(isset($achievement))
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>
                    Titre du projet en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_title"
                    value="{{ old('ar_title', $achievement->ar_title ?? '') }}"
                    placeholder="مثال: موقع متجر إلكتروني"
                    dir="rtl"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Titre du projet en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_title"
                    value="{{ old('fr_title', $achievement->fr_title ?? '') }}"
                    placeholder="Ex: Site e-commerce Horizon PC"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Titre du projet en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_title"
                    value="{{ old('en_title', $achievement->en_title ?? '') }}"
                    placeholder="Ex: Horizon PC e-commerce website"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Type du projet en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_type"
                    value="{{ old('ar_type', $achievement->ar_type ?? '') }}"
                    placeholder="مثال: متجر إلكتروني"
                    dir="rtl"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Type du projet en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_type"
                    value="{{ old('fr_type', $achievement->fr_type ?? '') }}"
                    placeholder="Ex: E-commerce"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Type du projet en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_type"
                    value="{{ old('en_type', $achievement->en_type ?? '') }}"
                    placeholder="Ex: E-commerce"
                    required>
            </div>

            <div class="form-group full">
                <label>
                    Description courte en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_short_description"
                    maxlength="300"
                    value="{{ old('ar_short_description', $achievement->ar_short_description ?? '') }}"
                    placeholder="اكتب ملخصا قصيرا يظهر في بطاقة المشروع"
                    dir="rtl"
                    required>
            </div>

            <div class="form-group full">
                <label>
                    Description courte en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_short_description"
                    maxlength="300"
                    value="{{ old('fr_short_description', $achievement->fr_short_description ?? '') }}"
                    placeholder="Résumé court affiché dans la carte du projet"
                    required>
            </div>

            <div class="form-group full">
                <label>
                    Description courte en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_short_description"
                    maxlength="300"
                    value="{{ old('en_short_description', $achievement->en_short_description ?? '') }}"
                    placeholder="Short summary displayed inside the project card"
                    required>
            </div>

            <div class="form-group full">
                <label>Description détaillée en arabe</label>

                <textarea
                    name="ar_description"
                    rows="5"
                    dir="rtl"
                    placeholder="اشرح المشروع بالتفصيل: الهدف، التقنيات المستعملة، والنتيجة النهائية...">{{ old('ar_description', $achievement->ar_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Description détaillée en français</label>

                <textarea
                    name="fr_description"
                    rows="5"
                    placeholder="Décrivez le projet en détail : objectif, fonctionnalités, technologies utilisées et résultat final...">{{ old('fr_description', $achievement->fr_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Description détaillée en anglais</label>

                <textarea
                    name="en_description"
                    rows="5"
                    placeholder="Describe the project in detail: goal, features, technologies used, and final result...">{{ old('en_description', $achievement->en_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>URL du projet</label>

                <input
                    type="url"
                    name="project_url"
                    value="{{ old('project_url', $achievement->project_url ?? '') }}"
                    placeholder="https://example.com">
            </div>

            <div class="form-group full">
                <label>Image du projet</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*">

                <small class="field-help">
                    Image affichée dans la carte du projet. Formats recommandés : JPG, PNG ou WEBP.
                </small>

                @if(!empty($achievement?->image))
                    <div class="current-image">
                        <span>Image actuelle</span>
                        <img src="{{ asset('uploads/' . $achievement->image) }}" alt="Image du projet">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label class="checkbox-card">
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        {{ old('is_published', $achievement->is_published ?? true) ? 'checked' : '' }}>

                    <span class="checkbox-ui">
                        <i class="fas fa-check"></i>
                    </span>

                    <span>
                        <strong>Publier cette réalisation</strong>
                        <small>La réalisation sera visible sur le site public.</small>
                    </span>
                </label>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                <i class="fas fa-floppy-disk"></i>
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.achievements.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Annuler
            </a>
        </div>

    </form>
</div>
