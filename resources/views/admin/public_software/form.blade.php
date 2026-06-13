<div class="container">

    <form
        method="POST"
        action="{{ isset($software) ? route('admin.public_software.update', $software) : route('admin.public_software.store') }}"
        enctype="multipart/form-data">

        @csrf
        @if(isset($software))
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>
                    Nom du logiciel en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_name"
                    value="{{ old('ar_name', $software->ar_name ?? '') }}"
                    placeholder="مثال: نظام تسيير المخزون"
                    dir="rtl"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Nom du logiciel en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_name"
                    value="{{ old('fr_name', $software->fr_name ?? '') }}"
                    placeholder="Ex: Système de gestion de stock"
                    required>
            </div>

            <div class="form-group">
                <label>
                    Nom du logiciel en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_name"
                    value="{{ old('en_name', $software->en_name ?? '') }}"
                    placeholder="Ex: Inventory management system"
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
                    value="{{ old('ar_short_description', $software->ar_short_description ?? '') }}"
                    placeholder="اكتب ملخصا قصيرا يظهر في بطاقة البرنامج"
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
                    value="{{ old('fr_short_description', $software->fr_short_description ?? '') }}"
                    placeholder="Résumé court affiché dans la carte du logiciel"
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
                    value="{{ old('en_short_description', $software->en_short_description ?? '') }}"
                    placeholder="Short summary displayed inside the software card"
                    required>
            </div>

            <div class="form-group full">
                <label>
                    Description détaillée en arabe
                    <span class="required">*</span>
                </label>

                <textarea
                    name="ar_description"
                    rows="5"
                    dir="rtl"
                    placeholder="اشرح البرنامج بالتفصيل: الوظائف، الفوائد، وطريقة استعماله..."
                    required>{{ old('ar_description', $software->ar_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>
                    Description détaillée en français
                    <span class="required">*</span>
                </label>

                <textarea
                    name="fr_description"
                    rows="5"
                    placeholder="Décrivez le logiciel en détail : fonctionnalités, avantages et cas d’utilisation..."
                    required>{{ old('fr_description', $software->fr_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>
                    Description détaillée en anglais
                    <span class="required">*</span>
                </label>

                <textarea
                    name="en_description"
                    rows="5"
                    placeholder="Describe the software in detail: features, benefits, and use cases..."
                    required>{{ old('en_description', $software->en_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Image du logiciel</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*">

                <small class="field-help">
                    Image principale affichée sur le site. Formats recommandés : JPG, PNG ou WEBP.
                </small>

                @if(!empty($software?->image))
                    <div class="current-image">
                        <span>Image actuelle</span>
                        <img src="{{ asset('uploads/' . $software->image) }}" alt="Image du logiciel">
                    </div>
                @endif
            </div>

            <div class="form-group full">
                <label>Icône du logiciel</label>

                <input
                    type="file"
                    name="icon"
                    accept="image/*">

                <small class="field-help">
                    Icône utilisée pour représenter le logiciel. Format carré recommandé.
                </small>

                @if(!empty($software?->icon))
                    <div class="current-image icon-preview">
                        <span>Icône actuelle</span>
                        <img src="{{ asset('uploads/' . $software->icon) }}" alt="Icône du logiciel">
                    </div>
                @endif
            </div>

            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    {{ old('is_published', $software->is_published ?? true) ? 'checked' : '' }}>
                    Publier ce logiciel
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.public_software.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>
