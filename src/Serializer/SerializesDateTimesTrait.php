<?php

namespace App\Serializer;

use DateTimeImmutable;

trait SerializesDateTimesTrait
{
    /**
     * @param \DateTimeImmutable|null $dateTime
     * @return null|string
     */
    protected function serializeDate(?DateTimeImmutable $dateTime): ?string
    {
        if (is_null($dateTime)) {
            return null;
        }

        return $dateTime->format(DateTimeImmutable::ATOM);
    }
}