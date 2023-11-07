<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class TeamsPermission
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! empty($user)) {
            setPermissionsTeamId(session('branch_id'), $user->branch?->id);
        }

        return $next($request);
    }
}
