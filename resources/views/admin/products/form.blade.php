<div class="container">

    <form method="POST" action="{{ $action }}">
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

            <div class="form-group full">
                <label>Description</label>
                <textarea name="description"
                          placeholder="Entrez la description du produit">{{ old('description', $product->description ?? '') }}</textarea>
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

