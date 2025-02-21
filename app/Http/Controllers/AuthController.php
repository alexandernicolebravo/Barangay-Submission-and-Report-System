<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Redirect based on user role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'cluster' => redirect()->route('cluster.index'),
                'barangay' => redirect()->route('barangay.submissions'),
                default => redirect()->route('login')->with('error', 'Unauthorized'),
            };
        }

        return back()->withErrors(['email' => 'Invalid login credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
