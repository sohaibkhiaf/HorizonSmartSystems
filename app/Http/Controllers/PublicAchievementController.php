<?php

namespace App\Http\Controllers;

use App\Models\PublicAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicAchievementController extends Controller
{
public function index()
    {
        $achievements = PublicAchievement::latest()->paginate(9);

        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.achievements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ar_title' => ['required', 'string', 'max:255'],
            'en_title' => ['required', 'string', 'max:255'],
            'fr_title' => ['required', 'string', 'max:255'],
            'ar_type' => ['nullable', 'string', 'max:100'],
            'en_type' => ['nullable', 'string', 'max:100'],
            'fr_type' => ['nullable', 'string', 'max:100'],
            'ar_short_description' => ['required', 'string', 'max:300'],
            'en_short_description' => ['required', 'string', 'max:300'],
            'fr_short_description' => ['required', 'string', 'max:300'],
            'ar_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],
            'fr_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('achievements', 'public');
        }

        PublicAchievement::create($data);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Réalisation créée avec succès.');
    }

    public function edit(PublicAchievement $achievement)
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, PublicAchievement $achievement)
    {
        $data = $request->validate([
            'ar_title' => ['required', 'string', 'max:255'],
            'en_title' => ['required', 'string', 'max:255'],
            'fr_title' => ['required', 'string', 'max:255'],
            'ar_type' => ['nullable', 'string', 'max:100'],
            'en_type' => ['nullable', 'string', 'max:100'],
            'fr_type' => ['nullable', 'string', 'max:100'],
            'ar_short_description' => ['required', 'string', 'max:300'],
            'en_short_description' => ['required', 'string', 'max:300'],
            'fr_short_description' => ['required', 'string', 'max:300'],
            'ar_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],
            'fr_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);


        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($achievement->image) {
                Storage::disk('public')->delete($achievement->image);
            }

            $data['image'] = $request->file('image')->store('achievements', 'public');
        }

        $achievement->update($data);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Réalisation mise à jour avec succès.');
    }

    public function destroy(PublicAchievement $achievement)
    {
        $achievement->delete();

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Réalisation supprimée avec succès.');
    }
}
