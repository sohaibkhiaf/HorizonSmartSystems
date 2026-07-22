<div class="container">

    <form
        method="POST"
        action="{{ isset($service)
            ? route('admin.public_services.update', $service)
            : route('admin.public_services.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        @if(isset($service))
            @method('PUT')
        @endif

        <div class="form-grid">

            {{-- Arabic name --}}
            <div class="form-group">
                <label>
                    Nom du service en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_name"
                    value="{{ old('ar_name', $service->ar_name ?? '') }}"
                    placeholder="مثال: تطوير المواقع الإلكترونية"
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
                    value="{{ old('ar_slug', $service->ar_slug ?? '') }}"
                    placeholder="مثال: تطوير-المواقع-الإلكترونية"
                    dir="rtl"
                    required
                >

                <small class="field-help">
                    Utilisez uniquement des lettres, des chiffres et des tirets.
                </small>

                @error('ar_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- French name --}}
            <div class="form-group">
                <label>
                    Nom du service en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_name"
                    value="{{ old('fr_name', $service->fr_name ?? '') }}"
                    placeholder="Ex: Création de sites web"
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
                    value="{{ old('fr_slug', $service->fr_slug ?? '') }}"
                    placeholder="Ex: creation-de-sites-web"
                    required
                >

                <small class="field-help">
                    Exemple d’URL : /fr/services/creation-de-sites-web
                </small>

                @error('fr_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English name --}}
            <div class="form-group">
                <label>
                    Nom du service en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_name"
                    value="{{ old('en_name', $service->en_name ?? '') }}"
                    placeholder="Ex: Website development"
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
                    value="{{ old('en_slug', $service->en_slug ?? '') }}"
                    placeholder="Ex: website-development"
                    required
                >

                <small class="field-help">
                    Exemple d’URL : /en/services/website-development
                </small>

                @error('en_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Short Arabic description --}}
            <div class="form-group full">
                <label>
                    Description courte en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_short_description"
                    maxlength="300"
                    value="{{ old('ar_short_description', $service->ar_short_description ?? '') }}"
                    placeholder="اكتب ملخصا قصيرا للخدمة يظهر في البطاقات"
                    dir="rtl"
                    required
                >

                @error('ar_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Short French description --}}
            <div class="form-group full">
                <label>
                    Description courte en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_short_description"
                    maxlength="300"
                    value="{{ old('fr_short_description', $service->fr_short_description ?? '') }}"
                    placeholder="Résumé court affiché dans la carte du service"
                    required
                >

                @error('fr_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Short English description --}}
            <div class="form-group full">
                <label>
                    Description courte en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_short_description"
                    maxlength="300"
                    value="{{ old('en_short_description', $service->en_short_description ?? '') }}"
                    placeholder="Short summary displayed inside the service card"
                    required
                >

                @error('en_short_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Detailed Arabic description --}}
            <div class="form-group full">
                <label>
                    Description détaillée en arabe
                    <span class="required">*</span>
                </label>

                <textarea
                    name="ar_description"
                    rows="5"
                    dir="rtl"
                    placeholder="اشرح الخدمة بالتفصيل: ماذا تقدم، لمن هي موجهة، وما هي فوائدها..."
                    required
                >{{ old('ar_description', $service->ar_description ?? '') }}</textarea>

                @error('ar_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Detailed French description --}}
            <div class="form-group full">
                <label>
                    Description détaillée en français
                    <span class="required">*</span>
                </label>

                <textarea
                    name="fr_description"
                    rows="5"
                    placeholder="Décrivez le service en détail : objectif, avantages, public ciblé..."
                    required
                >{{ old('fr_description', $service->fr_description ?? '') }}</textarea>

                @error('fr_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Detailed English description --}}
            <div class="form-group full">
                <label>
                    Description détaillée en anglais
                    <span class="required">*</span>
                </label>

                <textarea
                    name="en_description"
                    rows="5"
                    placeholder="Describe the service in detail: purpose, benefits, and target audience..."
                    required
                >{{ old('en_description', $service->en_description ?? '') }}</textarea>

                @error('en_description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Image --}}
            <div class="form-group full">
                <label>Image du service</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*"
                >

                <small class="field-help">
                    Formats recommandés : JPG, PNG ou WEBP.
                </small>

                @error('image')
                    <small class="field-error">{{ $message }}</small>
                @enderror

                @if(!empty($service?->image))
                    <div class="current-image">
                        <span>Image actuelle</span>

                        <img
                            src="{{ asset('uploads/' . $service->image) }}"
                            alt="Image du service"
                        >
                    </div>
                @endif
            </div>

            {{-- Font Awesome icon --}}
            <div class="form-group full">
                <label>Icône Font Awesome</label>

                <input
                    type="text"
                    name="fa_icon"
                    value="{{ old('fa_icon', $service->fa_icon ?? '') }}"
                    placeholder="Ex: fa-solid fa-globe"
                >

                <small class="field-help">
                    Pour un service lié à la boutique, utilisez :
                    <code>fa-solid fa-credit-card</code>
                </small>

                @error('fa_icon')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Service type --}}
            <div class="form-group full">
                <label>
                    Type du service
                    <span class="required">*</span>
                </label>

                <select name="type" required>
                    <option
                        value=""
                        disabled
                        @selected(old('type', $service->type ?? '') === '')
                    >
                        Sélectionner un type
                    </option>

                    <option
                        value="main"
                        @selected(old('type', $service->type ?? '') === 'main')
                    >
                        Service principal
                    </option>

                    <option
                        value="other"
                        @selected(old('type', $service->type ?? '') === 'other')
                    >
                        Service secondaire
                    </option>
                </select>

                @error('type')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- Publication --}}
            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $service->is_published ?? true))
                >

                Publier ce service
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a
                href="{{ route('admin.public_services.index') }}"
                class="btn-secondary"
            >
                Annuler
            </a>
        </div>

    </form>
</div>