@extends('admin.layouts.admin')

@section('title', 'Modifier le hero')
@section('page_title', 'Modifier le hero')
@section('page_subtitle', 'Mettre à jour la section principale du site')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/admin/public_hero/show.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

<div class="container">

    <form
        method="POST"
        action="{{ route('admin.public_hero.update', $hero) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="form-grid">

            {{-- Labels --}}
            <div class="form-group">
                <label>Label en arabe</label>

                <input
                    type="text"
                    name="ar_label"
                    value="{{ old('ar_label', $hero->ar_label ?? '') }}"
                    placeholder="مثال: وكالة رقمية"
                    dir="rtl">
            </div>

            <div class="form-group">
                <label>Label en français</label>

                <input
                    type="text"
                    name="fr_label"
                    value="{{ old('fr_label', $hero->fr_label ?? '') }}"
                    placeholder="Ex: Agence digitale">
            </div>

            <div class="form-group full">
                <label>Label en anglais</label>

                <input
                    class="half"
                    type="text"
                    name="en_label"
                    value="{{ old('en_label', $hero->en_label ?? '') }}"
                    placeholder="Ex: Digital agency">
            </div>

            {{-- Titles --}}
            <div class="form-group">
                <label>
                    Titre en arabe
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="ar_title"
                    value="{{ old('ar_title', $hero->ar_title ?? '') }}"
                    placeholder="مثال: نحوّل أفكارك إلى واقع رقمي"
                    dir="rtl"
                    required>
                    <small class="field-help">
                        Pour mettre une partie du texte en couleur différente, entourez-la avec :
                        <code>&lt;span class="hero-focus"&gt;votre texte&lt;/span&gt;</code>
                    </small>
            </div>

            <div class="form-group">
                <label>
                    Titre en français
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="fr_title"
                    value="{{ old('fr_title', $hero->fr_title ?? '') }}"
                    placeholder="Ex: Nous transformons vos idées en solutions digitales"
                    required>
                    <small class="field-help">
                        Pour mettre une partie du texte en couleur différente, entourez-la avec :
                        <code>&lt;span class="hero-focus"&gt;votre texte&lt;/span&gt;</code>
                    </small>
            </div>

            <div class="form-group">
                <label>
                    Titre en anglais
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="en_title"
                    value="{{ old('en_title', $hero->en_title ?? '') }}"
                    placeholder="Ex: We turn your ideas into digital solutions"
                    required>
                    <small class="field-help">
                        Pour mettre une partie du texte en couleur différente, entourez-la avec :
                        <code>&lt;span class="hero-focus"&gt;votre texte&lt;/span&gt;</code>
                    </small>
            </div>

            {{-- Descriptions --}}
            <div class="form-group full">
                <label>Description en arabe</label>

                <textarea
                    name="ar_description"
                    rows="5"
                    dir="rtl"
                    placeholder="صِف خدمات الوكالة وما يميّزها عن غيرها...">{{ old('ar_description', $hero->ar_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Description en français</label>

                <textarea
                    name="fr_description"
                    rows="5"
                    placeholder="Décrivez les services de l'agence et ce qui la distingue...">{{ old('fr_description', $hero->fr_description ?? '') }}</textarea>
            </div>

            <div class="form-group full">
                <label>Description en anglais</label>

                <textarea
                    name="en_description"
                    rows="5"
                    placeholder="Describe the agency's services and what sets it apart...">{{ old('en_description', $hero->en_description ?? '') }}</textarea>
            </div>

            {{-- Image --}}
            <div class="form-group full">
                <label>Image du hero</label>

                <input
                    type="file"
                    name="image"
                    accept="image/*">

                <small class="field-help">
                    Image principale affichée dans le hero du site. Formats recommandés : JPG, PNG ou WEBP.
                </small>

                @if(!empty($hero->image))
                    <div class="current-image">
                        <span>Image actuelle</span>
                        <img src="{{ asset('uploads/' . $hero->image) }}" alt="Image du hero">
                    </div>
                @endif
            </div>

            {{-- Social links --}}
            <div class="form-group">
                <label>Lien Facebook</label>

                <input
                    type="url"
                    name="facebook_url"
                    value="{{ old('facebook_url', $hero->facebook_url ?? '') }}"
                    placeholder="https://facebook.com/votreagence">
            </div>

            <div class="form-group">
                <label>Lien Instagram</label>

                <input
                    type="url"
                    name="instagram_url"
                    value="{{ old('instagram_url', $hero->instagram_url ?? '') }}"
                    placeholder="https://instagram.com/votreagence">
            </div>

            <div class="form-group">
                <label>Lien WhatsApp</label>

                <input
                    type="url"
                    name="whatsapp_url"
                    value="{{ old('whatsapp_url', $hero->whatsapp_url ?? '') }}"
                    placeholder="https://wa.me/213XXXXXXXXX">
            </div>

            <div class="form-group">
                <label>Lien LinkedIn</label>

                <input
                    type="url"
                    name="linked_in_url"
                    value="{{ old('linked_in_url', $hero->linked_in_url ?? '') }}"
                    placeholder="https://linkedin.com/company/votreagence">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-main">
                {{ $buttonText ?? 'Enregistrer' }}
            </button>

            <a href="{{ route('admin.public_hero.show', $hero) }}" class="btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>

@endsection
