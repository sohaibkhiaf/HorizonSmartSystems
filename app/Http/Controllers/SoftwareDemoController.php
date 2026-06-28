<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\CustomerDemo;
use App\Models\PublicSoftware;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SoftwareDemoController extends Controller
{
    public function index()
    {
        $demos = CustomerDemo::with('publicSoftware')
            ->paginate(15);

        return view('admin.software_demos.index', compact('demos'));
    }

    public function create()
    {
        $publicSoftware = PublicSoftware::orderBy('fr_name')->get();

        return view('admin.software_demos.create', compact('publicSoftware'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $this->uploadImage($request);

        CustomerDemo::create($data);

        return redirect()
            ->route('admin.software_demos.index')
            ->with('success', 'Démo créée avec succès.');
    }

    public function edit(CustomerDemo $demo)
    {
        $demo->load('publicSoftware');

        $publicSoftware = PublicSoftware::orderBy('fr_name')->get();

        return view('admin.software_demos.edit', compact('demo', 'publicSoftware'));
    }

    public function update(Request $request, CustomerDemo $demo)
    {
        $data = $this->validatedData($request);

        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $this->uploadImage($request, $demo);

        $demo->update($data);

        return redirect()
            ->route('admin.software_demos.index')
            ->with('success', 'Démo mise à jour avec succès.');
    }

    public function destroy(CustomerDemo $demo)
    {
        $demo->delete();

        return redirect()
            ->route('admin.software_demos.index')
            ->with('success', 'Démo supprimée avec succès.');
    }

    private function validatedData(Request $request): array
    {
        $softwareTable = (new PublicSoftware())->getTable();

        return $request->validate([
            'public_software_id' => [
                'required',
                Rule::exists($softwareTable, 'id'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'demo_link' => ['nullable', 'url', 'max:1000'],
            'admin_note' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function uploadImage(Request $request, ?CustomerDemo $demo = null): ?string
    {
        if (!$request->hasFile('image')) {
            return $demo?->image;
        }

        if ($demo?->image && File::exists(public_path('uploads/' . $demo->image))) {
            File::delete(public_path('uploads/' . $demo->image));
        }

        $directory = public_path('uploads/software_demos');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');

        $filename = time() . '_' . Str::random(12) . '.' . $file->getClientOriginalExtension();

        $file->move($directory, $filename);

        return 'software_demos/' . $filename;
    }
}
