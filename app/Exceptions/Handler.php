<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Exception;
use AppLog;
use App\Traits\ResponseCodeTrait;

class Handler extends ExceptionHandler
{
    use ResponseCodeTrait;

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
        $this->renderable(function(Exception $e, $request) {
            return $this->handleException($request, $e);
        });
    }

    public function handleException($request, Exception $exception)
    {
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $response = $this->getResponseCode(104);
            $status = $response['http_code'];
        } elseif ($exception instanceof NotFoundHttpException) {
            $response = $this->getResponseCode(108);
            $status = $response['http_code'];
        } elseif ($exception instanceof RecordNotFoundException) {
            $response = $this->getResponseCode(104);
            $status = $response['http_code'];
        } elseif ($exception instanceof AuthenticationFailedException) {
            $response = $this->getResponseCode(106);
            $response['response_code'] = $exception->getCode();
            $response['message'] = $exception->getMessage();
            $status = 401;
        }elseif ($exception instanceof ServiceCallFailedException) {
            $response = $this->getResponseCode(102);
            $response['message'] = $exception->getMessage();
            $status = 500;
        } elseif ($exception instanceof ValidationFailedException) {
            $response = $this->getResponseCode(101);
            $response['message'] = $exception->getMessage();
            $status = 400;
        } elseif ($exception instanceof ServiceErrorException) {
            $exception_code = $exception->getCode();
            $exception_code = (!empty($exception_code)) ? $exception_code : 102;
            $response = $this->getResponseCode($exception_code);
            $response['message'] = $exception->getMessage();
            $status = 500;
        } elseif ($exception instanceof ExternalCallFailedException) {
            $exception_code = $exception->getCode();
            $exception_code = (!empty($exception_code)) ? $exception_code : 102;
            $response = $this->getResponseCode($exception_code);
            $response['message'] = $exception->getMessage();
            $status = 500;
        } elseif ($exception instanceof ServiceTimeoutException) {
            $exception_code = $exception->getCode();
            $exception_code = (!empty($exception_code)) ? $exception_code : 102;
            $response = $this->getResponseCode($exception_code);
            $response['message'] = $exception->getMessage();
            $status = 504;
        } elseif ($exception instanceof ServiceUnavailableException) {
            $exception_code = $exception->getCode();
            $exception_code = (!empty($exception_code)) ? $exception_code : 102;
            $response = $this->getResponseCode($exception_code);
            $response['message'] = $exception->getMessage();
            $status = 503;
        } else {
            $response = $this->getResponseCode(101);
            $response['message'] = $exception->getMessage();
            $status = 500;
        }

        unset($response['http_code']);
        return response()->json($response, $status);
    }
}
