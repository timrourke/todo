<?php

declare(strict_types=1);

namespace App\Deserializer;

use DateTimeImmutable;

trait DeserializesDateTimesTrait
{
    /**
     * @param null|string $date
     * @return \DateTimeImmutable|null
     */
    public function deserializeDate(?string $date): ?DateTimeImmutable
    {
        if (is_null($date)) {
            return null;
        }

        return DateTimeImmutable::createFromFormat(
            DateTimeImmutable::ATOM,
            $date
        );
    }
}