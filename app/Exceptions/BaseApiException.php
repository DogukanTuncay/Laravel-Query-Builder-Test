<?php
abstract class BaseApiException extends Exception
{
    protected int $statusCode = 500;
    protected string $errorCode = 'GENERAL_ERROR';
    protected array $errors = [];

    public function __construct(string $message = '', array $errors = [], ?Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

