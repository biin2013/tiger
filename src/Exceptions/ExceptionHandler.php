<?php

namespace Biin2013\Tiger\Exceptions;

use Biin2013\Tiger\Support\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Throwable;

class ExceptionHandler extends Handler
{
    public function render($request, Throwable $e): JsonResponse
    {
        $data = [];
        $code = 1;
        $status = 500;

        switch ($e) {
            case $e instanceof AuthenticationException:
                $code = 401;
                $status = 401;
                $message = 'Unauthenticated';
                break;
            case $e instanceof UnauthorizedException:
                $code = 403;
                $status = 403;
                $message = 'permission denied';
                break;
            case $e instanceof MethodNotAllowedException:
            case $e instanceof NotFoundHttpException:
                $code = 404;
                $status = 404;
                $message = 'not found';
                break;
            case $e instanceof ThrottleRequestsException:
                $code = 429;
                $status = 429;
                $message = 'to many requests';
                break;
            case $e instanceof ValidationException:
            case $e instanceof RuntimeException:
                $code = $e->getCode();
                $status = $e->getStatus();
                $message = $e->getMessage();
                $data = $e->getData();
                break;
            default:
                $message = $e->getMessage();
                $data['file'] = basename($e->getFile());
                $data['line'] = $e->getLine();
        }

        return Response::error($code, $message, $data, $status);
    }
}