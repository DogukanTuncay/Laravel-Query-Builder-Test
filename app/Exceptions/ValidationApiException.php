<?php

namespace App\Exceptions;

use Exception;

use BaseApiException;
class ValidationApiException extends BaseApiException
{
    protected int $statusCode = 422;
    protected string $errorCode = 'VALIDATION_ERROR';
}
