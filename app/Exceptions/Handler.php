<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        if (($request->is('api/*') || $request->expectsJson()) && $e instanceof NotFoundHttpException) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        return parent::render($request, $e);
    }
}
