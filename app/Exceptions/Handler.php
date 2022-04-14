<?php

namespace App\Exceptions;

use App\Http\Responses\ExceptionResponse;
use App\Http\Responses\ValidationExceptionResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return new ValidationExceptionResponse($exception);
        }

        if ($exception instanceof AuthenticationException) {
            $exception = new AuthenticationException(
                'Запрос требует аутентификации.',
                $exception->guards(),
                $exception->redirectTo()
            );
        }

        if ($exception instanceof AuthorizationException) {
            $exception = new AuthorizationException(trans($exception->getMessage()), previous:$exception);
        }

        return parent::render($request, $exception);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return (new ExceptionResponse($e))->toResponse($request);
    }
}
