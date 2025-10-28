<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $allowedRoles = collect($roles)
            ->map(fn (string $role) => str_replace('-', '_', strtolower($role)))
            ->map(fn (string $role) => Role::tryFrom($role))
            ->filter()
            ->all();

        if (empty($allowedRoles)) {
            $allowedRoles = [Role::SuperAdmin];
        }

        $userRole = $user->role;

        if ($userRole instanceof Role && in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        if (is_string($userRole)) {
            $enumRole = Role::tryFrom($userRole);

            if ($enumRole && in_array($enumRole, $allowedRoles, true)) {
                return $next($request);
            }
        }

        $attributes = $user->getAttributes();

        if (array_key_exists('is_admin', $attributes) && $attributes['is_admin']) {
            if (in_array(Role::SuperAdmin, $allowedRoles, true)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
