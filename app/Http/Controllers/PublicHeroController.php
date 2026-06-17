<?php

namespace App\Http\Controllers;

use App\Models\PublicHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicHeroController extends Controller
{
    public function show(PublicHero $hero)
    {
        // view
        return view('admin/public_hero/show', compact('hero'));
    }

    public function update(Request $request, PublicHero $hero)
    {
        // validate
        $data = $request->validate([

            'ar_label' => ['nullable', 'string', 'max:255'],
            'en_label' => ['nullable', 'string', 'max:255'],
            'fr_label' => ['nullable', 'string', 'max:255'],

            'ar_title' => ['required', 'string', 'max:255'],
            'en_title' => ['required', 'string', 'max:255'],
            'fr_title' => ['required', 'string', 'max:255'],

            'ar_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],
            'fr_description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_url' => ['nullable', 'url', 'max:255'],
            'linked_in_url' => ['nullable', 'url', 'max:255'], 
        ]);

        // image
        if ($request->hasFile('image')) {
            if ($hero->image) {
                Storage::disk('public')->delete($hero->image);
            }

            $data['image'] = $request->file('image')->store('hero', 'public');
        }

        // update
        $hero->update($data);

        // redirect
        return redirect()
            ->route('admin.public_hero.show', $hero)
            ->with('success', 'Mise à jour du hero réussie.');
    }
}
