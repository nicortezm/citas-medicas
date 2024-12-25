<?php

namespace App\Exceptions;

use Exception;

class AppointmentException extends Exception
{
    protected $statusCode;

    public function __construct(string $message = "Error en la cita", int $statusCode = 400, Exception $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $this->getMessage(),
                'code' => $this->getCode(),
            ]
        ], $this->statusCode);
    }
}
