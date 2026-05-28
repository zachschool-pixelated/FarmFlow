<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::query();

        if (Auth::user()->role === 'admin') {
            $query->where('role', 'manager');
        } elseif (Auth::user()->role === 'manager') {
            $query->where('role', 'supplier');
        }

        return view('users.index', [
            'users' => $query->orderByDesc('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        $suppliers = \App\Models\Supplier::query()->orderBy('name', 'asc')->get();
        return view('users.create', compact('suppliers'));
    }

    public function store(\Illuminate\Http\Request $request): RedirectResponse
    {
        $roleRules = Auth::user()->role === 'admin'
            ? 'required|string|in:manager'
            : 'required|string|in:supplier';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => $roleRules,
            'supplier_id' => 'nullable|exists:suppliers,id|required_if:role,supplier',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plain_password' => $validated['password'],
            'role' => $validated['role'],
            'supplier_id' => $validated['role'] === 'supplier' ? $validated['supplier_id'] : null,
        ]);

        return redirect()->route('users.index')->with('status', 'User created successfully.');
    }

    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user): View
    {
        if (Auth::user()->role === 'admin' && $user->role !== 'manager') {
            abort(403, 'Admins can only edit manager accounts.');
        }

        if (Auth::user()->role === 'manager' && $user->role !== 'supplier') {
            abort(403, 'Managers can only edit supplier accounts.');
        }

        $suppliers = \App\Models\Supplier::query()->orderBy('name', 'asc')->get();
        return view('users.edit', compact('user', 'suppliers'));
    }

    public function update(\Illuminate\Http\Request $request, User $user): RedirectResponse
    {
        if (Auth::user()->role === 'admin' && $user->role !== 'manager') {
            abort(403, 'Admins can only edit manager accounts.');
        }

        if (Auth::user()->role === 'manager' && $user->role !== 'supplier') {
            abort(403, 'Managers can only edit supplier accounts.');
        }

        $roleRules = Auth::user()->role === 'admin'
            ? 'required|string|in:manager'
            : 'required|string|in:supplier';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role' => $roleRules,
            'supplier_id' => 'nullable|exists:suppliers,id|required_if:role,supplier',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'supplier_id' => $validated['role'] === 'supplier' ? $validated['supplier_id'] : null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
            $data['plain_password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()->route('users.index')->with('status', 'User updated successfully.');
    }

    public function toggleRestrict(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            abort(403, 'You cannot restrict your own account.');
        }

        if ($user->role === 'admin') {
            abort(403, 'Admins cannot be restricted.');
        }

        if (Auth::user()->role === 'admin' && $user->role !== 'manager') {
            abort(403, 'Admins can only restrict manager accounts.');
        }

        if (Auth::user()->role === 'manager' && $user->role !== 'supplier') {
            abort(403, 'Managers can only restrict supplier accounts.');
        }

        $user->update(['is_restricted' => !$user->is_restricted]);

        $status = $user->is_restricted ? 'Account restricted successfully.' : 'Account unrestricted successfully.';
        return redirect()->route('users.index')->with('status', $status);
    }
}