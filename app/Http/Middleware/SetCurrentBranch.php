<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentBranch
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !session()->has('current_branch_id')) {
            $user = auth()->user();

            if ($user->branches->isNotEmpty()) {
                $defaultBranch = $user->branches()->wherePivot('is_default', true)->first()
                    ?? $user->branches->first();

                session([
                    'current_branch_id' => $defaultBranch->id,
                    'current_branch_name' => $defaultBranch->name,
                ]);
            }
        }

        return $next($request);
    }
}
