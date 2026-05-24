<div class="form-grid">

    <div class="form-group">
        <label>Title</label>
        <input 
            type="text" 
            name="title" 
            value="{{ old('title', $achievement->title ?? '') }}" 
            placeholder="Enter project title (e.g. Company Website)"
            required
        >
    </div>

    <div class="form-group">
        <label>Type</label>
        <select name="type">
            <option value="" disabled selected>Select project type</option>
            <option value="web" {{ old('type', $achievement->type ?? '') === "web" ? 'selected' : '' }}>
                Website
            </option>
            <option value="ecommerce" {{ old('type', $achievement->type ?? '') === "ecommerce" ? 'selected' : '' }}>
                E-commerce
            </option>
            <option value="software" {{ old('type', $achievement->type ?? '') === "software" ? 'selected' : '' }}>
                Software
            </option>
        </select>
    </div>

    <div class="form-group full">
        <label>Short Description</label>
        <input 
            type="text" 
            name="short_description" 
            maxlength="300" 
            value="{{ old('short_description', $achievement->short_description ?? '') }}" 
            placeholder="Brief summary (max 300 characters)"
            required
        >
    </div>

    <div class="form-group full">
        <label>Description</label>
        <textarea 
            name="description" 
            rows="5"
            placeholder="Write a detailed description of the project..."
        >{{ old('description', $achievement->description ?? '') }}</textarea>
    </div>

    <div class="form-group full">
        <label>Project URL</label>
        <input 
            type="url" 
            name="project_url" 
            value="{{ old('project_url', $achievement->project_url ?? '') }}" 
            placeholder="https://example.com"
        >
    </div>

    <div class="form-group full">
        <label>Image</label>
        <input 
            type="file" 
            name="image" 
            accept="image/*"
        >
        <small>Upload a project image (JPG, PNG, etc.)</small>

        @if(!empty($achievement?->image))
            <div class="current-image">
                <img src="{{ asset('uploads/' . $achievement->image) }}" alt="">
            </div>
        @endif
    </div>

    <div class="form-group full">
        <label class="checkbox-row">
            <input 
                type="checkbox" 
                name="is_published" 
                value="1"
                {{ old('is_published', $achievement->is_published ?? true) ? 'checked' : '' }}>
            <span>Published (visible on website)</span>
        </label>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn-main">
        {{ $buttonText ?? 'Save' }}
    </button>

    <a href="{{ route('admin.achievements.index') }}" class="btn-secondary">
        Cancel
    </a>
</div>

