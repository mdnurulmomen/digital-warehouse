<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
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
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {

            return response()->json(['errors'=>["noUser" => 'Entry for '.str_replace('App\\Models\\', '', $exception->getModel()).' not found']], 422);  // 404 (later)
            
        }

        else if ($exception instanceof TokenMismatchException) {
            
            return redirect()->back()->withErrors(['Your session has been expired, please try again']);

        }

        else if ($exception instanceof HttpException)  {
            return response()->json(['errors'=>["noPermission" => 'You dont have permission for this action, please reload']], 403); 
        }

        return parent::render($request, $exception);
    }
}
