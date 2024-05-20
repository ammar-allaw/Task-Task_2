<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        QueryException::class => LogLevel::ERROR,
        // Add more exception types and log levels as needed
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
  /**
 * Render an exception into an HTTP response.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Throwable  $exception
 * @return \Symfony\Component\HttpFoundation\Response
 *
 * @throws \Throwable
 */
public function render($request, Throwable $exception)
{
    if ($exception instanceof NotFoundHttpException) {
        $errorMessage= $exception->getMessage();
        return response()->json([
            'status' => 'error',
            'message' => $errorMessage,
        ], 404);


   

    }

    if ($exception instanceof QueryException) {
        $errorMessage= $exception->getMessage();
        return response()->json([
            'status' => 'error',
            'message' => $errorMessage,
            'error' => $exception->getMessage(),
        ], 500);
    }

    if ($exception instanceof ValidationException) {
        $errorMessage= $this->getErrorMessage($exception);
        return response()->json([
            'status' => 'error',
            'message' => $errorMessage,
            'errors' => $exception->errors(),
        ],$exception->status);
    }


    return parent::render($request, $exception);
}




    protected function getErrorMessage(Throwable $exception)
    {
        // Customize the error message key here
        return $exception->getMessage() ? $exception->getMessage() : 'Unknown Error';
    }

    protected function getStatuesOfException(Throwable $exception)
    {
        // Customize the error message key here
        return  method_exists($exception, 'getStatusCode');
    }
/**
 * Get the error message from the exception.
 *
 * @param  \Throwable  $exception
 * @return string
 */

}