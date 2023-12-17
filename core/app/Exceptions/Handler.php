<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

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
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            // $request->expectsJson()
            // If the request expects JSON, return a JSON response
            return $this->renderJsonResponse($exception);
        }

        return parent::render($request, $exception);
    }

    protected function renderJsonResponse(Throwable $exception)
    {
        $status = 500;
        $file = $exception->getFile(); // Get the line number
        $line = $exception->getLine(); // Get the file target

        Log::info("detail path file and line : ". json_encode([
            'file' => $file,
            'line' => $line,
        ]));

        if ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
        }

        return response()->json([
            'error' => [
                'code'    => $status,
                'message' => $exception->getMessage(),
            ],
        ], $status);
    }
}
