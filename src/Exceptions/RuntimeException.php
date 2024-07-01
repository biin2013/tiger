<?php

namespace Biin2013\Tiger\Exceptions;

use Biin2013\Tiger\Exceptions\Concerns\DataStatusAttributes;
use Exception;
use Throwable;

class RuntimeException extends Exception
{
    use DataStatusAttributes;

    /**
     * @param int $code
     * @param string $message
     * @param array<string, mixed> $data
     * @param int $status
     * @param Throwable|null $previous
     */
    public function __construct(
        int       $code,
        string    $message = 'error',
        array     $data = [],
        int       $status = 400,
        Throwable $previous = null
    )
    {
        $this->data = $data;
        $this->status = $status;

        parent::__construct($message, $code, $previous);
    }
}