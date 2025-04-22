<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        $user->roles()->attach($validated['roles']);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }
    
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        
        $user->update($userData);
        $user->roles()->sync($validated['roles']);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }
    
    public function destroy(User $user)
    {
        // Don't allow deleting your own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account');
        }
        
        $user->roles()->detach();
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}