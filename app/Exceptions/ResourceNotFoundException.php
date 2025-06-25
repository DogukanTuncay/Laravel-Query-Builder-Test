<?php

namespace App\Exceptions;

use Exception;
use BaseApiException;
// ResourceNotFoundException.php
class ResourceNotFoundException extends BaseApiException
{
    protected int $statusCode = 404;
    protected string $errorCode = 'RESOURCE_NOT_FOUND';
}
