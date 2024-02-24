<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function(TokenInvalidException $e, $request){
            return response()->json(['error'=>'Invalid token'], 401);
        });
        $this->renderable(function (TokenExpiredException $e, $request) {
            return response()->json(['error'=>'Token has Expired'], 468);
        });
        $this->renderable(function (JWTException $e, $request) {
            return response()->json(['error'=>'Token not parsed'], 401);
        });
        $this->renderable(function (TokenBlacklistedException $e, $request) {
            return response()->json(['error'=>'Token has been blacklisted'], 401);
        });
        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json(['error'=>'Unauthenticated'], 401);
        });
    }
}
