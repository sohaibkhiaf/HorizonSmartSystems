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
        $roles = ['USER', 'ADMIN', 'OPERATOR', 'CUSTOMER'];

        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role') && in_array($request->role, $roles)) {
            $query->where('role', $request->role);
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        // view
        return view('admin/users/create');
    }

    public function store(Request $request)
    {
        // validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'role' => 'required',
        ]);

        // create
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // redirect
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function edit(User $user)
    {
        // view
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // validate
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'company' =>  'nullable|string',
            'role' => 'required',
        ]);

        // columns
        $data = $request->only(['name', 'email', 'phone', 'company', 'role']);

        // password
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // update
        $user->update($data);

        // redirect
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour');
    }

    public function destroy(User $user)
    {
        // delete   
        $user->delete();

        // back
        return back()->with('success', 'Utilisateur supprimé');
    }
}

