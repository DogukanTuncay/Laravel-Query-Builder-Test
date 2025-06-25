<?php

namespace App\Exceptions;

use Exception;
use BaseApiException;
class UnauthorizedException extends BaseApiException
{
    protected int $statusCode = 401;
    protected string $errorCode = 'UNAUTHORIZED';
}
