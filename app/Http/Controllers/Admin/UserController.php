<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(array_column(Role::cases(), 'value'))],
        ]);

        if ($user->is($request->user()) && $data['role'] !== Role::SuperAdmin->value) {
            return back()->withErrors([
                'role' => 'No puedes quitar tu propio rol de super administrador.',
            ]);
        }

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('status', 'Rol del usuario actualizado correctamente.');
    }
}

