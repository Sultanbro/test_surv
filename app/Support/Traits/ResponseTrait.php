<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /**
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return JsonResponse
     */
    public function response(string $message, mixed $data = '', int $code = Response::HTTP_OK, $status = true): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message'       =>  $message,
            'data'          =>  $data,
        ], $code);
    }

    /**
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     */
    public function jsonApiErrorResponse(array $errors, int $code = Response::HTTP_BAD_REQUEST, $status = false): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'errors'    =>  $errors,
        ], $code);
    }

    /**
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function defaultErrorResponse(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => [],
        ], $code);
    }
}
