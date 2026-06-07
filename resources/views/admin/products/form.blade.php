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
                       value="{{ old('name', $product->name ?? '') }}"
                       placeholder="Entrez le nom du produit"
                       required>
            </div>

            <div class="form-group">
                <label>Prix</label>
                <input type="number" 
                       name="price"
                       value="{{ old('price', $product->price ?? '') }}"
                       placeholder="Entrez le prix"
                       step="0.01"
                       min="0"
                       required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" id="" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="key" {{ $product && $product->type == 'key' ? 'selected' :''  }}>Clé</option>
                    <option value="account" {{ $product && $product->type == 'account' ? 'selected' :''  }}>Compte</option>
                    <option value="invitation" {{ $product && $product->type == 'invitation' ? 'selected' :''  }}>Invitation</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Description</label>
                <textarea name="description"
                          placeholder="Entrez la description du produit">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Image</label>
                <input 
                    type="file" 
                    name="image" 
                    accept="image/*">
                <small>Télécharger une image du projet (JPG, PNG, etc.)</small>

                @if(!empty($product?->image))
                    <div class="current-image">
                        <img src="{{ asset('uploads/' . $product->image) }}" alt="">
                    </div>
                @endif
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $button }}
            </button>

            <a href="{{ route('admin.products.index') }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>

</div>

