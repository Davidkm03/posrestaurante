<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $this->setDefaultBranch();

        // Redirigir según el rol del usuario
        return redirect()->intended($this->getRedirectRoute());
    }

    public function loginWithPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'size:4'],
        ]);

        $user = User::where('pin', $request->pin)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'PIN incorrecto o usuario inactivo',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->setDefaultBranch();

        return response()->json([
            'success' => true,
            'redirect' => $this->getRedirectRoute(),
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function setDefaultBranch(): void
    {
        $user = Auth::user();

        if ($user && $user->branches->isNotEmpty()) {
            $defaultBranch = $user->branches()->wherePivot('is_default', true)->first()
                ?? $user->branches->first();

            session([
                'current_branch_id' => $defaultBranch->id,
                'current_branch_name' => $defaultBranch->name,
            ]);
        }
    }

    protected function getRedirectRoute(): string
    {
        $user = Auth::user();
        $position = strtolower($user->position ?? '');

        return match($position) {
            'mesero' => route('waiter.index'),
            'cajero' => route('pos.index'),
            'cocina' => route('kitchen.index'),
            'gerente', 'administrador' => route('admin.dashboard'),
            default => route('admin.dashboard'),
        };
    }
}
