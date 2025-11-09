<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow access if user has an active subscription or is on trial
        if ($user && ($user->subscribed('default') || $user->onTrial('default'))) {
            return $next($request);
        }

        // Redirect to subscription page if not subscribed
        return redirect()->route('subscription.index')
            ->with('error', 'You need an active subscription to access this feature. Start your 7-day free trial today!');
    }
}
