<?php

namespace App\Exceptions;

use Exception;
use BaseApiException;
// ForbiddenException.php
class ForbiddenException extends BaseApiException
{
    protected int $statusCode = 403;
    protected string $errorCode = 'FORBIDDEN';
}
