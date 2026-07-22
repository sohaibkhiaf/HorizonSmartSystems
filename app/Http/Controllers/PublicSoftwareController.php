<?php

namespace App\Http\Controllers;

use App\Models\PublicSoftware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicSoftwareController extends Controller
{
    /**
     * Display the software list.
     */
    public function index(): View
    {
        $public_software = PublicSoftware::query()
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view(
            'admin.public_software.index',
            compact('public_software')
        );
    }

    /**
     * Display the creation form.
     */
    public function create(): View
    {
        return view('admin.public_software.create');
    }

    /**
     * Store a new software entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->normalizeSlugInputs($request);

        $data = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('software', 'public');
        }

        if ($request->hasFile('icon')) {
            $data['icon'] = $request
                ->file('icon')
                ->store('software_icons', 'public');
        }

        PublicSoftware::create($data);

        return redirect()
            ->route('admin.public_software.index')
            ->with('success', 'Logiciel créé avec succès.');
    }

    /**
     * Display the editing form.
     */
    public function edit(PublicSoftware $software): View
    {
        return view(
            'admin.public_software.edit',
            compact('software')
        );
    }

    /**
     * Update an existing software entry.
     */
    public function update(
        Request $request,
        PublicSoftware $software
    ): RedirectResponse {
        $this->normalizeSlugInputs($request);

        $data = $request->validate(
            $this->validationRules($software),
            $this->validationMessages()
        );

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($software->image) {
                Storage::disk('public')->delete($software->image);
            }

            $data['image'] = $request
                ->file('image')
                ->store('software', 'public');
        }

        if ($request->hasFile('icon')) {
            if ($software->icon) {
                Storage::disk('public')->delete($software->icon);
            }

            $data['icon'] = $request
                ->file('icon')
                ->store('software_icons', 'public');
        }

        $software->update($data);

        return redirect()
            ->route('admin.public_software.index')
            ->with('success', 'Logiciel mis à jour avec succès.');
    }

    /**
     * Soft-delete a software entry.
     */
    public function destroy(
        PublicSoftware $software
    ): RedirectResponse {
        $software->delete();

        return redirect()
            ->route('admin.public_software.index')
            ->with('success', 'Logiciel supprimé avec succès.');
    }

    /**
     * Return validation rules.
     */
    private function validationRules(
        ?PublicSoftware $software = null
    ): array {
        return [
            'ar_name' => [
                'required',
                'string',
                'max:255',
            ],

            'fr_name' => [
                'required',
                'string',
                'max:255',
            ],

            'en_name' => [
                'required',
                'string',
                'max:255',
            ],

            'ar_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule('ar_slug', $software),
            ],

            'fr_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule('fr_slug', $software),
            ],

            'en_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule('en_slug', $software),
            ],

            'ar_short_description' => [
                'required',
                'string',
                'max:300',
            ],

            'fr_short_description' => [
                'required',
                'string',
                'max:300',
            ],

            'en_short_description' => [
                'required',
                'string',
                'max:300',
            ],

            'ar_description' => [
                'required',
                'string',
            ],

            'fr_description' => [
                'required',
                'string',
            ],

            'en_description' => [
                'required',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'icon' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Create the unique slug validation rule.
     */
    private function uniqueSlugRule(
        string $column,
        ?PublicSoftware $software = null
    ) {
        $rule = Rule::unique('public_software', $column);

        if ($software) {
            $rule->ignore($software->getKey());
        }

        return $rule;
    }

    /**
     * Normalize submitted slugs before validation.
     */
    private function normalizeSlugInputs(Request $request): void
    {
        $normalizedSlugs = [];

        foreach (['ar_slug', 'fr_slug', 'en_slug'] as $field) {
            if (!$request->filled($field)) {
                continue;
            }

            $normalizedSlugs[$field] = $this->normalizeSlug(
                (string) $request->input($field)
            );
        }

        $request->merge($normalizedSlugs);
    }

    /**
     * Normalize one slug while preserving Unicode letters.
     */
    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);

        $slug = mb_strtolower($slug, 'UTF-8');

        // Replace spaces and underscores with hyphens.
        $slug = preg_replace('/[\s_]+/u', '-', $slug);

        // Remove unsupported characters.
        $slug = preg_replace('/[^\p{L}\p{N}-]+/u', '', $slug);

        // Replace repeated hyphens with one hyphen.
        $slug = preg_replace('/-+/u', '-', $slug);

        return trim((string) $slug, '-');
    }

    /**
     * Return custom validation messages.
     */
    private function validationMessages(): array
    {
        return [
            'ar_slug.required' => 'Le slug arabe est obligatoire.',
            'ar_slug.unique' => 'Ce slug arabe est déjà utilisé.',
            'ar_slug.regex' => 'Le slug arabe doit contenir uniquement des lettres, des chiffres et des tirets.',

            'fr_slug.required' => 'Le slug français est obligatoire.',
            'fr_slug.unique' => 'Ce slug français est déjà utilisé.',
            'fr_slug.regex' => 'Le slug français doit contenir uniquement des lettres, des chiffres et des tirets.',

            'en_slug.required' => 'Le slug anglais est obligatoire.',
            'en_slug.unique' => 'Ce slug anglais est déjà utilisé.',
            'en_slug.regex' => 'Le slug anglais doit contenir uniquement des lettres, des chiffres et des tirets.',
        ];
    }
}

