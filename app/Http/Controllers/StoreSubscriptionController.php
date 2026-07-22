<?php

namespace App\Http\Controllers;

use App\Models\StoreSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StoreSubscriptionController extends Controller
{
    /**
     * Display the subscriptions list.
     */
    public function index(Request $request): View
    {
        $subscriptions = StoreSubscription::query()
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim((string) $request->input('search'));

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('ar_slug', 'like', '%' . $search . '%')
                            ->orWhere('fr_slug', 'like', '%' . $search . '%')
                            ->orWhere('en_slug', 'like', '%' . $search . '%');
                    });
                }
            )
            ->when(
                $request->filled('category'),
                fn ($query) => $query->where(
                    'category',
                    $request->input('category')
                )
            )
            ->when(
                $request->filled('type'),
                fn ($query) => $query->where(
                    'type',
                    $request->input('type')
                )
            )
            ->when(
                $request->input('is_published') !== null
                    && $request->input('is_published') !== '',
                fn ($query) => $query->where(
                    'is_published',
                    (bool) $request->integer('is_published')
                )
            )
            ->orderByDesc('priority')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.store_subscriptions.index',
            compact('subscriptions')
        );
    }

    /**
     * Display the creation form.
     */
    public function create(): View
    {
        return view('admin.store_subscriptions.create');
    }

    /**
     * Store a new subscription.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->normalizeSlugInputs($request);

        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $validated['is_published'] = $request->boolean('is_published');
        $validated['priority'] = $validated['priority'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('subscriptions', 'public');
        }

        StoreSubscription::create($validated);

        return redirect()
            ->route('admin.store_subs.index')
            ->with(
                'success',
                'L’abonnement a été créé avec succès.'
            );
    }

    /**
     * Display the editing form.
     */
    public function edit(int $id): View
    {
        $subscription = StoreSubscription::findOrFail($id);

        return view(
            'admin.store_subscriptions.edit',
            compact('subscription')
        );
    }

    /**
     * Update an existing subscription.
     */
    public function update(
        Request $request,
        int $id
    ): RedirectResponse {
        $subscription = StoreSubscription::findOrFail($id);

        $this->normalizeSlugInputs($request);

        $validated = $request->validate(
            $this->validationRules($subscription),
            $this->validationMessages()
        );

        $validated['is_published'] = $request->boolean('is_published');
        $validated['priority'] = $validated['priority'] ?? 0;

        if ($request->hasFile('image')) {
            if ($subscription->image) {
                Storage::disk('public')->delete($subscription->image);
            }

            $validated['image'] = $request
                ->file('image')
                ->store('subscriptions', 'public');
        }

        $subscription->update($validated);

        return redirect()
            ->route('admin.store_subs.index')
            ->with(
                'success',
                'L’abonnement a été mis à jour avec succès.'
            );
    }

    /**
     * Soft-delete a subscription.
     */
    public function destroy(int $id): RedirectResponse
    {
        $subscription = StoreSubscription::findOrFail($id);

        $subscription->delete();

        return back()->with(
            'success',
            'L’abonnement a été supprimé avec succès.'
        );
    }

    /**
     * Return subscription validation rules.
     */
    private function validationRules(
        ?StoreSubscription $subscription = null
    ): array {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'ar_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule(
                    'ar_slug',
                    $subscription
                ),
            ],

            'fr_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule(
                    'fr_slug',
                    $subscription
                ),
            ],

            'en_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',
                $this->uniqueSlugRule(
                    'en_slug',
                    $subscription
                ),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'integer',
                'min:0',
            ],

            'discount' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'priority' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'type' => [
                'nullable',
                Rule::in([
                    'key',
                    'account',
                    'activation_link',
                ]),
            ],

            'category' => [
                'required',
                Rule::in([
                    'games',
                    'gift_cards',
                    'software',
                    'subscriptions',
                    'in_game_currency',
                    'other',
                ]),
            ],
        ];
    }

    /**
     * Return a unique slug validation rule.
     */
    private function uniqueSlugRule(
        string $column,
        ?StoreSubscription $subscription = null
    ) {
        $rule = Rule::unique(
            'store_subscriptions',
            $column
        );

        if ($subscription) {
            $rule->ignore($subscription->getKey());
        }

        return $rule;
    }

    /**
     * Normalize all submitted slug fields.
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
     * Normalize a slug while supporting Unicode letters.
     */
    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);

        $slug = mb_strtolower($slug, 'UTF-8');

        // Replace spaces and underscores with hyphens.
        $slug = preg_replace('/[\s_]+/u', '-', $slug);

        // Remove unsupported characters.
        $slug = preg_replace(
            '/[^\p{L}\p{N}-]+/u',
            '',
            $slug
        );

        // Replace repeated hyphens with one hyphen.
        $slug = preg_replace('/-+/u', '-', $slug);

        return trim((string) $slug, '-');
    }

    /**
     * Return validation messages.
     */
    private function validationMessages(): array
    {
        return [
            'ar_slug.required' =>
                'Le slug arabe est obligatoire.',

            'ar_slug.unique' =>
                'Ce slug arabe est déjà utilisé.',

            'ar_slug.regex' =>
                'Le slug arabe doit contenir uniquement des lettres, des chiffres et des tirets.',

            'fr_slug.required' =>
                'Le slug français est obligatoire.',

            'fr_slug.unique' =>
                'Ce slug français est déjà utilisé.',

            'fr_slug.regex' =>
                'Le slug français doit contenir uniquement des lettres, des chiffres et des tirets.',

            'en_slug.required' =>
                'Le slug anglais est obligatoire.',

            'en_slug.unique' =>
                'Ce slug anglais est déjà utilisé.',

            'en_slug.regex' =>
                'Le slug anglais doit contenir uniquement des lettres, des chiffres et des tirets.',
        ];
    }
}

