<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $team = User::whereIn('role', ['admin', 'vendedor'])->latest()->get();
        return view('admin.team.index', compact('team'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:6',
                'max:15',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ],
            'role'     => 'required|in:admin,vendedor',
        ], [
            'email.unique'    => 'Este e-mail já está cadastrado.',
            'password.max'    => 'A senha deve ter no máximo 15 caracteres.',
            'password.regex'  => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'token'    => Str::random(80),
        ]);

        return redirect()->route('admin.team.index')->with('success', 'Membro da equipe adicionado!');
    }

    public function promote(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role'  => 'required|in:admin,vendedor',
        ], [
            'email.exists' => 'Nenhuma conta encontrada com este e-mail.',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (in_array($user->role, ['admin', 'vendedor'], true)) {
            return back()->with('error', 'Esta conta já faz parte da equipe.')->withInput();
        }

        $user->update(['role' => $data['role']]);

        AuditLog::record('promote_team_member', "promoveu {$user->name} para {$data['role']}");

        return redirect()->route('admin.team.index')->with('success', "{$user->name} promovido(a) para a equipe!");
    }

    public function edit(User $member)
    {
        abort_unless(in_array($member->role, ['admin', 'vendedor'], true), 404);
        return view('admin.team.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        abort_unless(in_array($member->role, ['admin', 'vendedor'], true), 404);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $member->id,
            'password' => [
                'nullable',
                'confirmed',
                'min:6',
                'max:15',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ],
            'role'     => 'required|in:admin,vendedor',
        ], [
            'email.unique'   => 'Este e-mail já está cadastrado.',
            'password.max'   => 'A senha deve ter no máximo 15 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, um número e um caractere especial.',
        ]);

        if ($member->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'Você não pode remover seu próprio acesso de admin.')->withInput();
        }

        $member->name  = $data['name'];
        $member->email = $data['email'];
        $member->role  = $data['role'];
        if (!empty($data['password'])) {
            $member->password = Hash::make($data['password']);
        }
        $member->save();

        return redirect()->route('admin.team.index')->with('success', 'Membro da equipe atualizado!');
    }

    public function destroy(User $member)
    {
        abort_unless(in_array($member->role, ['admin', 'vendedor'], true), 404);

        if ($member->id === auth()->id()) {
            return back()->with('error', 'Você não pode remover sua própria conta.');
        }

        if ($member->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Não é possível remover o último administrador.');
        }

        $member->update(['role' => 'customer']);

        AuditLog::record('remove_team_member', "removeu {$member->name} da equipe");

        return back()->with('success', 'Acesso removido. A conta foi mantida como cliente.');
    }
}
