<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Exception $e, Request $request) {
            if ($request->wantsJson()) {
                return $this->handleApiExceptions($request, $e);
            }
            return abort(500, $e->getMessage());
        });
    }

    public function handleApiExceptions(Request $request, Exception $e)
    {
        $response = null;
        if ($e instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                $response = new JsonResponse(['message' => "Resource not found."], JsonResponse::HTTP_FORBIDDEN);
            }
        }
        if ($e instanceof TransactionFailedException) {
            $response = new JsonResponse(['message' => $e->getMessage()]);
        }
        return $response;
    }
}
