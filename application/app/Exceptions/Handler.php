<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($exception instanceof CustomException) {
            //
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json([
                'status' => [
                    'system_message' => $exception->getMessage(),
                    'message' => 'Data tidak ditemukan!',
                    'code' => 404,
                ]
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json([
                'status' => [
                    'system_message' => $exception->getMessage(),
                    'message' => 'Halaman tidak ditemukan!',
                    'code' => 404,
                ]
            ], 404);
        }

        if ($exception instanceof AccessDeniedHttpException  && $request->wantsJson()) {
            return response()->json([
                'status' => [
                    'system_message' => $exception->getMessage(),
                    'message' => 'Hanya Checker yang diizinkan untuk menambah aktivitas harian!',
                    'code' => 403,
                ]
            ], 403);
        }

        if ($exception instanceof AuthorizationException && $request->wantsJson()) {
            return response()->json([
                'message' => 'Aksi yang Anda lakukan dilarang oleh sistem! Silahkan hubungi administrator untuk mengetahui info lebih lanjut!',
                'status' => [
                    'message' => 'Aksi yang Anda lakukan dilarang oleh sistem! Silahkan hubungi administrator untuk mengetahui info lebih lanjut!',
                    'code'    => 403,
                ],
                'code'    => 403,
            ], 403);
        }

        if ($exception instanceof AuthorizationException) {
            abort(403, 'Unauthorized action.');
        }

        return parent::render($request, $exception);
    }
}
