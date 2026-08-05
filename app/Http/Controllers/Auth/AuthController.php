<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CpfValido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) return redirect()->route('account.dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['token' => Str::random(80)]);
            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            if (Auth::user()->isAdmin()) return redirect()->route('admin.dashboard');
            return redirect()->intended(route('account.dashboard'));
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'E-mail ou senha incorretos.'], 422);
        }

        return back()->withErrors(['email' => 'E-mail ou senha incorretos.'])->withInput();
    }

    public function registerForm()
    {
        if (Auth::check()) return redirect()->route('account.dashboard');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => [
                'required',
                'confirmed',
                'min:6',
                'max:15',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ],
            'id_document' => 'nullable|string|max:30',
            'phone'       => 'nullable|string',
            'birth_date'  => 'nullable|date',
        ], [
            'email.unique'   => 'Este e-mail já está cadastrado.',
            'password.max'   => 'A senha deve ter no máximo 15 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'id_document' => $request->id_document,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date ?: null,
            'role'       => 'customer',
            'token'      => Str::random(80),
        ]);

        Auth::login($user);
        return redirect()->route('account.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
