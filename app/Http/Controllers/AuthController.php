<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $email = $request->email;
        $password = $request->password;

        // Check credentials against hardcoded values
        $validEmail = 'harsukh21@gmail.com';
        $validPassword = 'Har#$785';

        if ($email === $validEmail && $password === $validPassword) {
            // Create session data for authenticated user
            Session::put('authenticated', true);
            Session::put('user', [
                'name' => 'Harsukh',
                'email' => $validEmail,
                'role' => 'Administrator',
                'avatar' => null
            ]);

            // Remember me functionality
            if ($request->has('remember')) {
                Session::put('remember', true);
            }

            return redirect()->route('dashboard')->with('success', 'Welcome back, Harsukh!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show dashboard (after login)
     */
    public function dashboard()
    {
        // Check if user is authenticated
        if (!Session::has('authenticated')) {
            return redirect()->route('login');
        }

        $user = Session::get('user');
        return view('dashboard', compact('user'));
    }
}

