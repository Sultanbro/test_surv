<?php

namespace App\Exceptions;

use App\Exceptions\Tariff\UsersLimitExceededException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Throwable $exception)
    {


        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException) {
            return redirect(config('app.url') . '/login');
        }

        if ($this->isHttpException($exception)) {
            $code = $exception->getStatusCode();

            return response()->view('errors.' . $code, [], $code);
        }


        return parent::render($request, $exception);
    }

    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $exception) {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => $exception->getMessage()
            ]);
        });

        $this->renderable(function (TransactionException $exception) {
            return response()->json([
                'status' => Response::HTTP_EXPECTATION_FAILED,
                'message' => $exception->getMessage()
            ]);
        });

        $this->renderable(function (UsersLimitExceededException $exception) {
            return response()->json([
                'status' => Response::HTTP_EXPECTATION_FAILED,
                'message' => $exception->getMessage()
            ]);
        });
        parent::register(); // TODO: Change the autogenerated stub
    }
}
