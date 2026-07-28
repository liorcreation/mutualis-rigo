<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Autorise uniquement les rôles transmis au middleware.
     *
     * @param  string  ...$roles  Valeurs de UserRole séparées par des virgules dans la route.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role;
        $allowedRoles = array_map(
            static fn (string $role): ?UserRole => UserRole::tryFrom($role),
            $roles
        );

        if ($userRole instanceof UserRole && in_array($userRole, $allowedRoles, true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Votre rôle ne permet pas d’accéder à cette ressource.');
        }

        return redirect()
            ->route('dashboard')
            ->with('error', 'Accès refusé : votre rôle ne permet pas d’accéder à cet espace.');
    }
}
