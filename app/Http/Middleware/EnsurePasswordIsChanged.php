<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Send users flagged with change_password to their profile until they set a new password.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->change_password) {
            return redirect()
                ->route('profile')
                ->with('status', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}
