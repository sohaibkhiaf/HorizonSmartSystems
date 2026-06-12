<div class="container">

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf

        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="form-group">
                <label>Nom du produit</label>
                <input type="text" 
                       name="name"
                       value="{{ old('name', $subscription->name ?? '') }}"
                       placeholder="Entrez le nom du produit"
                       required>
            </div>

            <div class="form-group">
                <label>Prix</label>
                <input type="number" 
                       name="price"
                       value="{{ old('price', $subscription->price ?? '') }}"
                       placeholder="Entrez le prix"
                       step="0.01"
                       min="0"
                       required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" id="" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="key" {{ $subscription && $subscription->type == 'key' ? 'selected' :''  }}>Clé</option>
                    <option value="account" {{ $subscription && $subscription->type == 'account' ? 'selected' :''  }}>Compte</option>
                    <option value="invitation" {{ $subscription && $subscription->type == 'invitation' ? 'selected' :''  }}>Invitation</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catégorie</label>
                <select name="category" id="" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="games" {{ $subscription && $subscription->category == 'games' ? 'selected' :''  }}>Jeux</option>
                    <option value="gift_cards" {{ $subscription && $subscription->category == 'gift_cards' ? 'selected' :''  }}>Cartes cadeaux</option>
                    <option value="software" {{ $subscription && $subscription->category == 'software' ? 'selected' :''  }}>Logiciel</option>
                    <option value="subscriptions" {{ $subscription && $subscription->category == 'subscriptions' ? 'selected' :''  }}>Abonnements</option>
                    <option value="in_game_currency" {{ $subscription && $subscription->category == 'in_game_currency' ? 'selected' :''  }}>Monnaie virtuelle</option>
                    <option value="other" {{ $subscription && $subscription->category == 'other' ? 'selected' :''  }}>Autre</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Description</label>
                <textarea name="description"
                          placeholder="Entrez la description du produit">{{ old('description', $subscription->description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*">
                <small>Télécharger une image du projet (JPG, PNG, etc.)</small>

                @if(!empty($subscription?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $subscription->image) }}" alt="">
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

