<div class="form-grid">

    <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="{{ old('title', $realisation->title ?? '') }}" required>
    </div>

    <div class="form-group">
        <label>Type</label>
        <input type="text" name="type" value="{{ old('type', $realisation->type ?? '') }}" placeholder="Web App, ERP, E-commerce...">
    </div>

    <div class="form-group full">
        <label>Short Description</label>
        <input type="text" name="short_description" maxlength="300" value="{{ old('short_description', $realisation->short_description ?? '') }}" required>
    </div>

    <div class="form-group full">
        <label>Description</label>
        <textarea name="description" rows="5">{{ old('description', $realisation->description ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label>Project URL</label>
        <input type="url" name="project_url" value="{{ old('project_url', $realisation->project_url ?? '') }}" placeholder="https://example.com">
    </div>

    <div class="form-group">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $realisation->sort_order ?? 0) }}">
    </div>

    <div class="form-group full">
        <label>Image</label>
        <input type="file" name="image" accept="image/*">

        @if(!empty($realisation?->image))
            <div class="current-image">
                <img src="{{ asset('storage/' . $realisation->image) }}" alt="">
            </div>
        @endif
    </div>

    <div class="form-group full">
        <label class="checkbox-row">
            <input type="checkbox" name="is_published" value="1"
                {{ old('is_published', $realisation->is_published ?? true) ? 'checked' : '' }}>
            <span>Published</span>
        </label>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn-main">
        {{ $buttonText ?? 'Save' }}
    </button>

    <a href="{{ route('admin.realisations.index') }}" class="btn-secondary">
        Cancel
    </a>
</div>

