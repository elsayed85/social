<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });

        $this->renderable(function (Exception $e, Request $request) {
            if ($request->wantsJson()) {
                return $this->handleException($request, $e);
            }
        });
    }

    public function handleException($request, Exception $e)
    {
        if ($e instanceof AuthorizationException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_UNAUTHORIZED,
                'type' => 'authorization'
            ],  Response::HTTP_UNAUTHORIZED);
        } elseif ($e instanceof TokenMismatchException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_FORBIDDEN,
                'type' => 'access_denied_token_mismatch',
            ],  Response::HTTP_FORBIDDEN);
        } elseif ($e instanceof AccessDeniedHttpException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_FORBIDDEN,
                'type' => 'access_denied',
            ],  Response::HTTP_FORBIDDEN);
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_METHOD_NOT_ALLOWED,
                'type' => 'method_not_allowed',
            ],  Response::HTTP_METHOD_NOT_ALLOWED);
        } elseif ($e instanceof AuthenticationException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
                'type' => 'authentication'
            ],  Response::HTTP_NETWORK_AUTHENTICATION_REQUIRE);
        } elseif ($e instanceof ModelNotFoundException) {
            return failed($e->getMessage(), [
                'error_code' => Response::HTTP_NOT_FOUND,
                'type' => 'model_not_found'
            ],  Response::HTTP_NOT_FOUND);
        } elseif ($e instanceof HttpExceptionInterface) {
            $code = $e->getStatusCode();
            return failed($e->getMessage(), [
                'error_code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTrace(),
            ],  $code);
        }
    }
}
