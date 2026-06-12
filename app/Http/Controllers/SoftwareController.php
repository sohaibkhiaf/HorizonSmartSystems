<?php

namespace App\Http\Controllers;

use App\Models\PublicSoftware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoftwareController extends Controller
{
    public function index()
    {
        $public_software = PublicSoftware::latest()->paginate(9);

        return view('admin.public_software.index', compact('public_software'));
    }

    public function create()
    {
        return view('admin.public_software.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ar_name' => ['required', 'string', 'max:255'],
            'fr_name' => ['required', 'string', 'max:255'],
            'en_name' => ['required', 'string', 'max:255'],

            'ar_short_description' => ['required', 'string', 'max:300'],
            'fr_short_description' => ['required', 'string', 'max:300'],
            'en_short_description' => ['required', 'string', 'max:300'],

            'ar_description' => ['nullable', 'string'],
            'fr_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('software', 'public');
        }


        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('software_icons', 'public');
        }

        PublicSoftware::create($data);

        return redirect()
            ->route('admin.publicSoftware.index')
            ->with('success', 'Logiciel créé avec succès.');
    }

    public function edit(PublicSoftware $software)
    {
        return view('admin.public_software.edit', compact('software'));
    }

    public function update(Request $request, PublicSoftware $software)
    {
        $data = $request->validate([
            'ar_name' => ['required', 'string', 'max:255'],
            'fr_name' => ['required', 'string', 'max:255'],
            'en_name' => ['required', 'string', 'max:255'],

            'ar_short_description' => ['required', 'string', 'max:300'],
            'fr_short_description' => ['required', 'string', 'max:300'],
            'en_short_description' => ['required', 'string', 'max:300'],

            'ar_description' => ['nullable', 'string'],
            'fr_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 
            
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($software->image) {
                Storage::disk('public')->delete($software->image);
            }

            $data['image'] = $request->file('image')->store('software', 'public');
        }

        if ($request->hasFile('icon')) {
            if ($software->icon) {
                Storage::disk('public')->delete($software->icon);
            }

            $data['icon'] = $request->file('icon')->store('software_icons', 'public');
        }

        $software->update($data);

        return redirect()
            ->route('admin.publicSoftware.index')
            ->with('success', 'Logiciel mise à jour avec succès.');
    }

    public function destroy(PublicSoftware $software)
    {
        $software->delete();

        return redirect()
            ->route('admin.publicSoftware.index')
            ->with('success', 'Logiciel supprimée avec succès.');
    }
}
