<div class="container">

    <form
        method="POST"
        action="{{ isset($software)
            ? route('admin.public_software.update', $software)
            : route('admin.public_software.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        @if(isset($software))
            @method('PUT')
        @endif

        <div class="form-grid">

            {{-- Arabic name --}}
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
                    required
                >

                @error('ar_name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Arabic slug --}}
            <div class="form-group">
                <label>
                    Slug arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_slug"
                    value="{{ old('ar_slug', $software->ar_slug ?? '') }}"
                    placeholder="مثال: نظام-تسيير-المخزون"
                    dir="rtl"
                    required
                >

                <small class="field-help">
                    Utilisez des lettres, des chiffres et des tirets.
                </small>

                @error('ar_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- French name --}}
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
                    required
                >

                @error('fr_name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- French slug --}}
            <div class="form-group">
                <label>
                    Slug français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_slug"
                    value="{{ old('fr_slug', $software->fr_slug ?? '') }}"
                    placeholder="Ex: systeme-de-gestion-de-stock"
                    required
                >

                <small class="field-help">
                    Exemple : /fr/logiciels/systeme-de-gestion-de-stock
                </small>

                @error('fr_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English name --}}
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
                    required
                >

                @error('en_name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English slug --}}
            <div class="form-group">
                <label>
                    Slug anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_slug"
                    value="{{ old('en_slug', $software->en_slug ?? '') }}"
                    placeholder="Ex: inventory-management-system"
                    required
                >

                <small class="field-help">
                    Exemple : /en/software/inventory-management-system
                </small>

                @error('en_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Arabic short description --}}
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
                    required
                >

                @error('ar_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- French short description --}}
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
                    required
                >

                @error('fr_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English short description --}}
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
                    required
                >

                @error('en_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Arabic detailed description --}}
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
                    required
                >{{ old('ar_description', $software->ar_description ?? '') }}</textarea>

                @error('ar_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- French detailed description --}}
            <div class="form-group full">
                <label>
                    Description détaillée en français
                    <span class="required">*</span>
                </label>

                <textarea
                    name="fr_description"
                    rows="5"
                    placeholder="Décrivez le logiciel en détail : fonctionnalités, avantages et cas d’utilisation..."
                    required
                >{{ old('fr_description', $software->fr_description ?? '') }}</textarea>

                @error('fr_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English detailed description --}}
            <div class="form-group full">
                <label>
                    Description détaillée en anglais
                    <span class="required">*</span>
                </label>

                <textarea
                    name="en_description"
                    rows="5"
                    placeholder="Describe the software in detail: features, benefits, and use cases..."
                    required
                >{{ old('en_description', $software->en_description ?? '') }}</textarea>

                @error('en_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Software image --}}
            <div class="form-group full">
                <label>Image du logiciel</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*"
                >

                <small class="field-help">
                    Image principale affichée sur le site. Formats recommandés :
                    JPG, PNG ou WEBP.
                </small>

                @error('image')
                    <small class="field-error">{{ $message }}</small>
                @enderror

                @if(!empty($software?->image))
                    <div class="current-image">
                        <span>Image actuelle</span>

                        <img
                            src="{{ asset('uploads/' . $software->image) }}"
                            alt="Image du logiciel"
                        >
                    </div>
                @endif
            </div>

            {{-- Software icon --}}
            <div class="form-group full">
                <label>Icône du logiciel</label>

                <input
                    type="file"
                    name="icon"
                    accept="image/*"
                >

                <small class="field-help">
                    Icône utilisée pour représenter le logiciel.
                    Format carré recommandé.
                </small>

                @error('icon')
                    <small class="field-error">{{ $message }}</small>
                @enderror

                @if(!empty($software?->icon))
                    <div class="current-image icon-preview">
                        <span>Icône actuelle</span>

                        <img
                            src="{{ asset('uploads/' . $software->icon) }}"
                            alt="Icône du logiciel"
                        >
                    </div>
                @endif
            </div>

            {{-- Publication --}}
            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $software->is_published ?? true))
                >

                Publier ce logiciel
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a
                href="{{ route('admin.public_software.index') }}"
                class="btn-secondary"
            >
                Annuler
            </a>
        </div>

    </form>
</div>

