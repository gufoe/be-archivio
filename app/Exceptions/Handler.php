<?php

namespace App\Exceptions;

use Exception;
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
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);

        $clean_trace = explode("\n", $exception->getTraceAsString());
        $clean_trace = collect($clean_trace)
        ->filter(function ($line) {
            return strpos($line, 'vendor/') === false;
        })
        ->map(function ($line) {
            $line = str_replace(base_path(), '', $line);
            $dots = strpos($line, '):');
            if ($dots !== false) {
                $line = substr($line, 0, $dots + 1);
            }
            return $line;
        })
        ->implode("\n");

        $route = \Route::getCurrentRoute();
        $uri = '/'.trim($route ? $route->uri() : (@$_SERVER['REQUEST_URI'] ?: @url()->full()), '/');
        $text = "Errore in archivio\n"
            ."URL: ".$uri."\n"
            ."METH: ".mb_strtoupper($route ? $route->methods()[0] : @$_SERVER['REQUEST_METHOD'])."\n"
            ."ERR: ".get_class($exception)." {$exception->getMessage()}\n"
            ."Trace:\n".$clean_trace."\n";
        notify_admin($text);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
