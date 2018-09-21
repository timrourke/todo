<?php

declare(strict_types=1);

namespace App\Tests\App\Serializer;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SerializesDateTimesTraitTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function shouldSerializeDateTime()
    {
        $impl = new SerializesDateTimesTraitImpl();

        $dateTime = new DateTimeImmutable();

        $actual = $impl->serialize($dateTime);

        $this->assertSame(
            $dateTime->format(DateTimeImmutable::ATOM),
            $actual
        );
    }

    /**
     * @test
     */
    public function shouldSerializeNullDateTimeAsNull()
    {
        $impl = new SerializesDateTimesTraitImpl();

        $dateTime = null;

        $actual = $impl->serialize($dateTime);

        $this->assertSame(
            null,
            $actual
        );
    }
}