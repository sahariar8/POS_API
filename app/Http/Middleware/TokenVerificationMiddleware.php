<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');
        if (!$token) {
            $token = $request->cookie('token');
        }
        $result = JWTToken::verifyToken($token);
        if ($result == 'UnAuthorized') {
            return response()->json(['status' => 'failed', 'message' => 'UnAuthorized'], 401);
        } else {
            // $request->attributes->add(['email' => $result]);
            $request->headers->set('email', $result);
            return $next($request);
        }
    }
}
