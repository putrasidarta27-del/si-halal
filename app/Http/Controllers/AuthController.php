<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'Admin LPH') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'Auditor') {
                return redirect()->route('auditor.dashboard');
            }
        }

        return view('auth.login');
    }

    /**
     * Handle login submission.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $user = User::where($isEmail ? 'email' : 'username', $loginInput)->first();

        if (!$user || !Hash::check($password, $user->password_hash)) {
            return back()
                ->withErrors(['login' => 'Username/email atau password yang Anda masukkan salah.'])
                ->withInput($request->only('login'));
        }

        if (!$user->status_aktif) {
            return back()
                ->withErrors(['login' => 'Akun Anda berstatus non-aktif. Silakan hubungi admin LPH.'])
                ->withInput($request->only('login'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role === 'Admin LPH') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->role === 'Auditor') {
            return redirect()->intended(route('auditor.dashboard'));
        }

        return redirect('/');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
