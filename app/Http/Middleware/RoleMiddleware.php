<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            abort(403, 'Akses ditolak. Akun tidak aktif.');
        }

        if (!empty($roles)) {
            $allowedRoles = array_map(fn($r) => UserRole::tryFrom($r), $roles);
            $allowedRoles = array_filter($allowedRoles);

            if (!in_array($user->role, $allowedRoles)) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
        }

        return $next($request);
    }
}
