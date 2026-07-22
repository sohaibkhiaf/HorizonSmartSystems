<?php

namespace App\Http\Controllers;

use App\Models\PublicService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicServiceController extends Controller
{
    /**
     * Display the services list.
     */
    public function index(): View
    {
        $public_services = PublicService::query()
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view(
            'admin.public_services.index',
            compact('public_services')
        );
    }

    /**
     * Display the creation form.
     */
    public function create(): View
    {
        return view('admin.public_services.create');
    }

    /**
     * Store a new service.
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
                ->store('services', 'public');
        }

        PublicService::create($data);

        return redirect()
            ->route('admin.public_services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Display the edit form.
     */
    public function edit(PublicService $service): View
    {
        return view(
            'admin.public_services.edit',
            compact('service')
        );
    }

    /**
     * Update an existing service.
     */
    public function update(
        Request $request,
        PublicService $service
    ): RedirectResponse {
        $this->normalizeSlugInputs($request);

        $data = $request->validate(
            $this->validationRules($service),
            $this->validationMessages()
        );

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $data['image'] = $request
                ->file('image')
                ->store('services', 'public');
        }

        $service->update($data);

        return redirect()
            ->route('admin.public_services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Soft-delete a service.
     */
    public function destroy(
        PublicService $service
    ): RedirectResponse {
        $service->delete();

        return redirect()
            ->route('admin.public_services.index')
            ->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Return form validation rules.
     */
    private function validationRules(
        ?PublicService $service = null
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
                $this->uniqueSlugRule('ar_slug', $service),
            ],

            'fr_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule('fr_slug', $service),
            ],

            'en_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule('en_slug', $service),
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

            'fa_icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                Rule::in([
                    'main',
                    'other',
                ]),
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Return a unique rule for a slug column.
     */
    private function uniqueSlugRule(
        string $column,
        ?PublicService $service = null
    ) {
        $rule = Rule::unique('public_services', $column);

        if ($service) {
            $rule->ignore($service->getKey());
        }

        return $rule;
    }

    /**
     * Normalize all submitted slugs before validation.
     */
    private function normalizeSlugInputs(Request $request): void
    {
        $normalized = [];

        foreach (['ar_slug', 'fr_slug', 'en_slug'] as $field) {
            if (!$request->filled($field)) {
                continue;
            }

            $normalized[$field] = $this->normalizeSlug(
                (string) $request->input($field)
            );
        }

        $request->merge($normalized);
    }

    /**
     * Normalize one slug without removing Unicode letters.
     */
    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);

        $slug = mb_strtolower($slug, 'UTF-8');

        $slug = preg_replace('/\s+/u', '-', $slug);

        $slug = preg_replace('/-+/u', '-', $slug);

        return trim((string) $slug, '-');
    }

    /**
     * Custom validation messages.
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