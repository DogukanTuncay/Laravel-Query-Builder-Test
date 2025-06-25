<?php

namespace App\Exceptions;

use Exception;
use BaseApiException;
class BusinessLogicException extends BaseApiException
{
    protected int $statusCode = 400;
    protected string $errorCode = 'BUSINESS_LOGIC_ERROR';
}
