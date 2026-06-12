<?php

namespace App\Http\Controllers;

use App\Models\PublicService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicServiceController extends Controller
{
    public function index()
    {
        $public_services = PublicService::latest()->paginate(9);

        return view('admin.public_services.index', compact('public_services'));
    }

    public function create()
    {
        return view('admin.public_services.create');
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

            'type' => ['required', 'in:main,other'],

            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        PublicService::create($data);

        return redirect()
            ->route('admin.publicServices.index')
            ->with('success', 'Service créé avec succès.');
    }

    public function edit(PublicService $service)
    {
        return view('admin.public_services.edit', compact('service'));
    }

    public function update(Request $request, PublicService $service)
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
            
            'type' => ['required', 'in:main,other'],

            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()
            ->route('admin.publicServices.index')
            ->with('success', 'Service mise à jour avec succès.');
    }

    public function destroy(PublicService $service)
    {
        $service->delete();

        return redirect()
            ->route('admin.publicServices.index')
            ->with('success', 'Service supprimée avec succès.');
    }
}
