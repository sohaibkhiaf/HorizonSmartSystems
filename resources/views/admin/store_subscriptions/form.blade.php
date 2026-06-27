@php
    $selectedType = old('type', $subscription->type ?? '');
    $selectedCategory = old('category', $subscription->category ?? '');
    $isPublished = old('is_published', $subscription->is_published ?? true);
@endphp

<div class="container">

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf

        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group full">
                <label>Nom de l’abonnement <span class="required">*</span></label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $subscription->name ?? '') }}"
                       placeholder="Entrez le nom de l’abonnement"
                       required>
            </div>

            <div class="form-group">
                <label>Prix <span class="required">*</span></label>
                <input type="number"
                       name="price"
                       value="{{ old('price', $subscription->price ?? '') }}"
                       placeholder="Entrez le prix"
                       step="1"
                       min="0"
                       required>
            </div>

            <div class="form-group">
                <label>Remise</label>
                <input type="number"
                       name="discount"
                       value="{{ old('discount', $subscription->discount ?? '') }}"
                       placeholder="Entrez la remise"
                       step="1"
                       min="0">
            </div>

            <div class="form-group">
                <label>Priorité</label>
                <input type="number"
                       name="priority"
                       value="{{ old('priority', $subscription->priority ?? 0) }}"
                       placeholder="Ex: 0, 10, 50, 100"
                       step="1"
                       min="0">

                <small>Plus la priorité est élevée, plus le produit apparaît en premier.</small>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <option value="">-- Sélectionner --</option>
                    <option value="key" {{ $selectedType == 'key' ? 'selected' : '' }}>Clé</option>
                    <option value="account" {{ $selectedType == 'account' ? 'selected' : '' }}>Compte</option>
                    <option value="invitation" {{ $selectedType == 'invitation' ? 'selected' : '' }}>Invitation</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catégorie <span class="required">*</span></label>
                <select name="category" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="games" {{ $selectedCategory == 'games' ? 'selected' : '' }}>Jeux</option>
                    <option value="gift_cards" {{ $selectedCategory == 'gift_cards' ? 'selected' : '' }}>Cartes cadeaux</option>
                    <option value="software" {{ $selectedCategory == 'software' ? 'selected' : '' }}>Logiciel</option>
                    <option value="subscriptions" {{ $selectedCategory == 'subscriptions' ? 'selected' : '' }}>Abonnements</option>
                    <option value="in_game_currency" {{ $selectedCategory == 'in_game_currency' ? 'selected' : '' }}>Monnaie virtuelle</option>
                    <option value="other" {{ $selectedCategory == 'other' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Description</label>
                <textarea name="description"
                          placeholder="Entrez la description de l’abonnement">{{ old('description', $subscription->description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Image</label>
                <input type="file" name="image" accept="image/*">

                <small>Télécharger une image de l’abonnement (JPG, PNG, WEBP, etc.)</small>

                @if(!empty($subscription?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $subscription->image) }}" alt="Image de l’abonnement">
                    </div>
                @endif
            </div>

            <label class="checkbox-label">
                <input type="checkbox"
                        name="is_published"
                        value="1"
                        {{ (bool) $isPublished ? 'checked' : '' }}>
                    Afficher ce produit dans le store
            </label>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $button }}
            </button>

            <a href="{{ route('admin.store_subs.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>

</div>

