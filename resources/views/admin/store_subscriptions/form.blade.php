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
                <label>Type <span class="required">*</span></label>
                <select name="type" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="key" {{ $subscription && $subscription->type == 'key' ? 'selected' : '' }}>Clé</option>
                    <option value="account" {{ $subscription && $subscription->type == 'account' ? 'selected' : '' }}>Compte</option>
                    <option value="invitation" {{ $subscription && $subscription->type == 'invitation' ? 'selected' : '' }}>Invitation</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catégorie <span class="required">*</span></label>
                <select name="category" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="games" {{ $subscription && $subscription->category == 'games' ? 'selected' : '' }}>Jeux</option>
                    <option value="gift_cards" {{ $subscription && $subscription->category == 'gift_cards' ? 'selected' : '' }}>Cartes cadeaux</option>
                    <option value="software" {{ $subscription && $subscription->category == 'software' ? 'selected' : '' }}>Logiciel</option>
                    <option value="subscriptions" {{ $subscription && $subscription->category == 'subscriptions' ? 'selected' : '' }}>Abonnements</option>
                    <option value="in_game_currency" {{ $subscription && $subscription->category == 'in_game_currency' ? 'selected' : '' }}>Monnaie virtuelle</option>
                    <option value="other" {{ $subscription && $subscription->category == 'other' ? 'selected' : '' }}>Autre</option>
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

