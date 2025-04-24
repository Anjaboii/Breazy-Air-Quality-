<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
                $request->session()->regenerate();

                // Check admin status using both property and method for reliability
                $user = Auth::user();
                if ($user->is_admin === 1 || $user->isAdmin()) {
                    Log::info('Admin login', ['user_id' => $user->id]);
                    return redirect()
                        ->intended(route('admin.dashboard'))
                        ->with('success', 'Welcome back, Administrator!');
                }

                return redirect()
                    ->intended('/')
                    ->with('success', 'Logged in successfully');
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Invalid credentials or account not found',
                ]);
                
        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'An error occurred during login',
                ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User logged out', ['user_id' => $user?->id]);
            
            return redirect('/')
                ->with('status', 'You have been successfully logged out');
                
        } catch (\Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage()
            ]);
            
            return redirect('/')
                ->with('error', 'There was a problem logging out');
        }
    }
}