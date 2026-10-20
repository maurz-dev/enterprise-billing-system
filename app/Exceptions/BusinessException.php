<?php

namespace AppExceptions;

use Exception;
use IlluminateHttpJsonResponse;

class BusinessException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode = 422)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
