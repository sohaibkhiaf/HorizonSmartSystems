<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
public function index()
    {
        $realisations = Achievement::latest()->paginate(9);

        return view('admin.realisations.index', compact('realisations'));
    }

    public function create()
    {
        return view('admin.realisations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'short_description' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('realisations', 'public');
        }

        Achievement::create($data);

        return redirect()
            ->route('admin.realisations.index')
            ->with('success', 'Realisation created successfully.');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.realisations.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'short_description' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);


        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($achievement->image) {
                Storage::disk('public')->delete($achievement->image);
            }

            $data['image'] = $request->file('image')->store('realisations', 'public');
        }

        $achievement->update($data);

        return redirect()
            ->route('admin.realisations.index')
            ->with('success', 'Realisation updated successfully.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return redirect()
            ->route('admin.realisations.index')
            ->with('success', 'Realisation deleted successfully.');
    }


}
