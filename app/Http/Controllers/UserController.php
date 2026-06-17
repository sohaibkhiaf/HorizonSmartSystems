<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private array $roles = ['USER', 'ADMIN', 'OPERATOR', 'CUSTOMER'];

    private function authUser()
    {
        return auth()->user();
    }

    private function canManageUsers(): bool
    {
        return in_array($this->authUser()->role, ['ADMIN', 'OPERATOR']);
    }

    private function isSelf(User $user): bool
    {
        return $this->authUser()->id === $user->id;
    }

    private function canEditUser(User $user): bool
    {
        if (! $this->canManageUsers()) {
            return false;
        }

        // Admin can edit himself, but role is locked in update()
        if ($this->isSelf($user) && $this->authUser()->role === 'ADMIN') {
            return true;
        }

        // Nobody can edit another admin
        return $user->role !== 'ADMIN';
    }

    private function canDeleteUser(User $user): bool
    {
        // Only ADMIN can delete users
        if ($this->authUser()->role !== 'ADMIN') {
            return false;
        }

        // Admin cannot delete himself
        if ($this->isSelf($user)) {
            return false;
        }

        // Admins cannot delete other admins
        if ($user->role === 'ADMIN') {
            return false;
        }

        return true;
    }

    private function canUseRole(string $role): bool
    {
        if ($this->authUser()->role === 'ADMIN') {
            return in_array($role, $this->roles);
        }

        if ($this->authUser()->role === 'OPERATOR') {
            return in_array($role, ['USER', 'OPERATOR', 'CUSTOMER']);
        }

        return false;
    }

    public function index(Request $request)
    {
        if (! $this->canManageUsers()) {
            abort(403);
        }

        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role') && in_array($request->role, $this->roles)) {
            $query->where('role', $request->role);
        }

        $users = $query
            ->paginate(10)
            ->withQueryString();

        $roles = $this->roles;

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        if (! $this->canManageUsers()) {
            abort(403);
        }

        $roles = $this->authUser()->role === 'ADMIN'
            ? $this->roles
            : ['USER', 'OPERATOR', 'CUSTOMER'];

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (! $this->canManageUsers()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'role' => 'required|in:USER,ADMIN,OPERATOR,CUSTOMER',
        ]);

        if (! $this->canUseRole($request->role)) {
            return back()
                ->withInput()
                ->with('error', 'Vous n’avez pas le droit de créer ce type d’utilisateur.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function edit(User $user)
    {
        if (! $this->canEditUser($user)) {
            abort(403);
        }

        $roles = $this->authUser()->role === 'ADMIN'
            ? $this->roles
            : ['USER', 'OPERATOR', 'CUSTOMER'];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (! $this->canEditUser($user)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'password' => 'nullable|min:6',
            'role' => 'nullable|in:USER,ADMIN,OPERATOR,CUSTOMER',
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone',
            'company',
        ]);

        // Admin editing himself: role cannot be changed
        if (! ($this->isSelf($user) && $user->role === 'ADMIN')) {
            if (! $request->filled('role')) {
                return back()
                    ->withInput()
                    ->with('error', 'Le rôle est obligatoire.');
            }

            if (! $this->canUseRole($request->role)) {
                return back()
                    ->withInput()
                    ->with('error', 'Vous n’avez pas le droit d’attribuer ce rôle.');
            }

            $data['role'] = $request->role;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour');
    }

    public function destroy(User $user)
    {
        if (! $this->canDeleteUser($user)) {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé');
    }
}
