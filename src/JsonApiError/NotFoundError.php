<?php

declare(strict_types=1);

namespace App\JsonApiError;

use Neomerx\JsonApi\Document\Error;

class NotFoundError extends Error
{
    public static function createForType(string $type, string $id): NotFoundError
    {
        return new NotFoundError(
            null,
            null,
            '404',
            '404',
            '404 Not Found',
            sprintf('No %s found with ID %s', $type, $id)
        );
    }
}