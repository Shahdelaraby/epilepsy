<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
{
    if (!$request->expectsJson()) {
        return null; // لا تقومي بتوجيه المستخدم لأي مكان
    }
}
protected function unauthenticated($request, array $guards)
{
    abort(response()->json(['message' => 'Unauthorized'], 401));
}
}
