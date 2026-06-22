<?php

namespace Alpa\Utils;

use Alpa\Utils\Exceptions\AlpaError;
use Alpa\Utils\Exceptions\AlpaAuthenticationError;
use Alpa\Utils\Exceptions\AlpaValidationError;
use Alpa\Utils\Exceptions\AlpaNotFoundError;
use Alpa\Utils\Exceptions\AlpaRateLimitError;
use Alpa\Utils\Exceptions\AlpaServerError;

class Errors
{
    public static function handle(int $statusCode, array $body): \Exception
    {
        $message = $body['message'] ?? "HTTP {$statusCode}";
        $code = $body['code'] ?? null;
        
        switch ($statusCode) {
            case 401:
                return new AlpaAuthenticationError($message);
            case 400:
                $details = $body['details'] ?? null;
                return new AlpaValidationError($message, $details);
            case 404:
                $resourceId = $body['id'] ?? null;
                return new AlpaNotFoundError('Recurso', $resourceId);
            case 429:
                return new AlpaRateLimitError($message);
            case 500:
            case 502:
            case 503:
                return new AlpaServerError($message);
            default:
                return new AlpaError($message, $code, $statusCode, $body);
        }
    }
}
