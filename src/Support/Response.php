<?php

namespace Biin2013\Tiger\Support;

use Illuminate\Http\JsonResponse;

class Response
{
    /**
     * format response
     *
     * @param int $code
     * @param string $message
     * @param array<mixed> $data
     * @return array<string, mixed>
     */
    private static function format(int $code, string $message = 'success', array $data = []): array
    {
        $scope = request()->header('error-scope', 'common');
        return compact('scope', 'code', 'message', 'data');
    }

    /**
     * json response
     *
     * @param array<mixed> $data
     * @param integer $status
     * @param array<mixed> $headers
     * @param int $options
     * @return JsonResponse
     */
    private static function json(
        array $data = [],
        int   $status = 200,
        array $headers = [],
        int   $options = JSON_UNESCAPED_UNICODE
    ): JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    /**
     * format json
     *
     * @param int $code
     * @param string $message
     * @param array<mixed> $data
     * @param integer $status
     * @param array<mixed> $headers
     * @param int $options
     * @return JsonResponse
     */
    private static function formatJson(
        int    $code,
        string $message = 'success',
        array  $data = [],
        int    $status = 200,
        array  $headers = [],
        int    $options = JSON_UNESCAPED_UNICODE
    ): JsonResponse
    {
        return self::json(self::format($code, $message, $data), $status, $headers, $options);
    }

    /**
     * success response
     *
     * @param array<mixed> $data
     * @param int $code
     * @param string $message
     * @param integer $status
     * @param array<mixed> $headers
     * @param int $options
     * @return JsonResponse
     */
    public static function success(
        array  $data = [],
        int    $code = 0,
        string $message = 'success',
        int    $status = 200,
        array  $headers = [],
        int    $options = JSON_UNESCAPED_UNICODE
    ): JsonResponse
    {
        return self::formatJson(
            $code,
            $message,
            $data,
            $status,
            $headers,
            $options
        );
    }

    /**
     * error response
     *
     * @param int $code
     * @param string $message
     * @param array<mixed> $data
     * @param integer $status
     * @param array<mixed> $headers
     * @param int $options
     * @return JsonResponse
     */
    public static function error(
        int    $code,
        string $message = 'error',
        array  $data = [],
        int    $status = 400,
        array  $headers = [],
        int    $options = JSON_UNESCAPED_UNICODE
    ): JsonResponse
    {
        return self::formatJson($code, $message, $data, $status, $headers, $options);
    }
}