@php
    $selectedType = old('type', $subscription->type ?? '');
    $selectedCategory = old('category', $subscription->category ?? '');
    $isPublished = old('is_published', $subscription->is_published ?? true);
@endphp

<div class="container">

    <form
        method="POST"
        action="{{ $action }}"
        enctype="multipart/form-data"
    >
        @csrf

        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group full">
                <label>
                    Nom de l’abonnement
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $subscription->name ?? '') }}"
                    placeholder="Entrez le nom de l’abonnement"
                    required
                >

                @error('name')
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
                    value="{{ old('ar_slug', $subscription->ar_slug ?? '') }}"
                    placeholder="مثال: اشتراك-مايكروسوفت-365"
                    dir="rtl"
                    required
                >

                <small>
                    Exemple : /ar/store/اشتراك-مايكروسوفت-365
                </small>

                @error('ar_slug')
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
                    value="{{ old('fr_slug', $subscription->fr_slug ?? '') }}"
                    placeholder="Ex: abonnement-microsoft-365"
                    required
                >

                <small>
                    Exemple : /fr/store/abonnement-microsoft-365
                </small>

                @error('fr_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            {{-- English slug --}}
            <div class="form-group full">
                <label>
                    Slug anglais
                    <span class="required">*</span>
                </label>

                <input
                    class="half"
                    type="text"
                    name="en_slug"
                    value="{{ old('en_slug', $subscription->en_slug ?? '') }}"
                    placeholder="Ex: microsoft-365-subscription"
                    required
                >

                <small>
                    Exemple : /en/store/microsoft-365-subscription
                </small>

                @error('en_slug')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    Prix
                    <span class="required">*</span>
                </label>

                <input
                    type="number"
                    name="price"
                    value="{{ old('price', $subscription->price ?? '') }}"
                    placeholder="Entrez le prix"
                    step="1"
                    min="0"
                    required
                >

                @error('price')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Remise</label>

                <input
                    type="number"
                    name="discount"
                    value="{{ old('discount', $subscription->discount ?? '') }}"
                    placeholder="Entrez la remise"
                    step="1"
                    min="0"
                >

                @error('discount')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Priorité</label>

                <input
                    type="number"
                    name="priority"
                    value="{{ old('priority', $subscription->priority ?? 0) }}"
                    placeholder="Ex: 0, 10, 50, 100"
                    step="1"
                    min="0"
                >

                <small>
                    Plus la priorité est élevée, plus le produit apparaît en premier.
                </small>

                @error('priority')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Type</label>

                <select name="type">
                    <option value="">-- Sélectionner --</option>

                    <option
                        value="key"
                        @selected($selectedType === 'key')
                    >
                        Clé
                    </option>

                    <option
                        value="account"
                        @selected($selectedType === 'account')
                    >
                        Compte
                    </option>

                    <option
                        value="activation_link"
                        @selected($selectedType === 'activation_link')
                    >
                        Lien d'activation
                    </option>
                </select>

                @error('type')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    Catégorie
                    <span class="required">*</span>
                </label>

                <select name="category" required>
                    <option value="">-- Sélectionner --</option>

                    <option
                        value="games"
                        @selected($selectedCategory === 'games')
                    >
                        Jeux
                    </option>

                    <option
                        value="gift_cards"
                        @selected($selectedCategory === 'gift_cards')
                    >
                        Cartes cadeaux
                    </option>

                    <option
                        value="software"
                        @selected($selectedCategory === 'software')
                    >
                        Logiciel
                    </option>

                    <option
                        value="subscriptions"
                        @selected($selectedCategory === 'subscriptions')
                    >
                        Abonnements
                    </option>

                    <option
                        value="in_game_currency"
                        @selected($selectedCategory === 'in_game_currency')
                    >
                        Monnaie virtuelle
                    </option>

                    <option
                        value="other"
                        @selected($selectedCategory === 'other')
                    >
                        Autre
                    </option>
                </select>

                @error('category')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group full">
                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Entrez la description de l’abonnement"
                >{{ old('description', $subscription->description ?? '') }}</textarea>

                @error('description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group full">
                <label>Image</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*"
                >

                <small>
                    Télécharger une image de l’abonnement :
                    JPG, JPEG, PNG ou WEBP.
                </small>

                @error('image')
                    <small class="field-error">{{ $message }}</small>
                @enderror

                @if(!empty($subscription?->image))
                    <div class="current-image">
                        <img
                            src="{{ asset('uploads/' . $subscription->image) }}"
                            alt="Image de l’abonnement"
                        >
                    </div>
                @endif
            </div>

            <label class="checkbox-label">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked((bool) $isPublished)
                >

                Afficher ce produit dans le store
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $button }}
            </button>

            <a
                href="{{ route('admin.store_subs.index') }}"
                class="btn-secondary"
            >
                Annuler
            </a>
        </div>

    </form>

</div>

