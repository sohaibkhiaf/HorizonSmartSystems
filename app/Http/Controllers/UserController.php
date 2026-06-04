<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        $query = User::query();

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function edit(User $user)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required'
        ]);

        $data = $request->only(['name', 'email', 'role']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }
        
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé');
    }
}

