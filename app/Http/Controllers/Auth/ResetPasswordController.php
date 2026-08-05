<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required', 'confirmed', 'min:6', 'max:15',
                'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[\W_]/',
            ],
        ], [
            'password.regex'     => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
            'password.min'       => 'A senha deve ter no mínimo 6 caracteres.',
            'password.max'       => 'A senha deve ter no máximo 15 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Senha redefinida com sucesso! Faça login.');
        }

        return back()->withErrors(['email' => 'O link é inválido ou expirou. Solicite um novo.']);
    }
}
