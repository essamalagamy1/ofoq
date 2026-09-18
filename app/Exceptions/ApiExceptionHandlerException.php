<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiExceptionHandlerException extends ExceptionHandler
{
    use ApiResponse;

    public function register(): void
    {
        if (request()->is('api/*')) {
            $this->renderable(function (NotFoundHttpException $e, $request): \Illuminate\Http\JsonResponse {
                return $this->responseNotFound();
            });

            $this->renderable(function (UnauthorizedHttpException $e, $request): \Illuminate\Http\JsonResponse {
                return $this->responseUnauthorized();
            });

            $this->renderable(function (AccessDeniedHttpException $e, $request): \Illuminate\Http\JsonResponse {
                return $this->responseForbidden();
            });

            $this->renderable(function (AuthenticationException $e, $request): \Illuminate\Http\JsonResponse {
                return $this->responseUnAuthenticated();
            });

            $this->renderable(function (MethodNotAllowedHttpException $e, $request): \Illuminate\Http\JsonResponse {
                return $this->responseMethodNotAllowed();
            });

            //			$this->renderable(function (\Exception $e, $request) {
            //				return $this->responseInternalError();
            //			});
        }

    }
}
