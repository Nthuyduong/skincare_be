<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JWTMiddleWare
{
    public function handle($request, Closure $next, $guard = null)
    {
        // Get the decrypted TOKEN set the default guard api
        if (empty($payload = Auth::factory()->buildClaimsCollection()->toPlainArray())) {
            return response()->json(['error' => 'Token not parsed'], 401);
        }
        // Which guard is used for automatic authentication
        if (empty($guard = is_null($guard) ? ($payload['guard'] ?? null) : $guard)) {
            return response()->json(['error' => 'Guard not found'], 401);
        }
        // Use a try wrapper to catch the TokenExpiredException thrown by the token expiration
        try {
            // Detect the login status of the user and it is the current database. If it is normal, the order must be the same, otherwise the token will not be updated automatically.
            if ((strcmp($guard, Auth::guard($guard)->getClaim('guard')) === 0) && Auth::guard($guard)->check()) {
                return $next($request);
            }
            return response()->json([
                'status' => 401,
                'msg' => 'Unauthenticated'
            ], 401
            );
        } catch (TokenExpiredException $exception) {
            return response()->json([
                'status' => 468,
                'error' => 'Token has Expired'
            ], 468);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 401,
                'error' => 'Token not parsed'
            ], 401);
        }
    }
}