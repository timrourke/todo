<?php

declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\TodoId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TodoIdTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function shouldCreateFromUuidString()
    {
        $originalUuid1 = Uuid::uuid1();

        $uuidString = $originalUuid1->toString();

        $todoId = TodoId::fromUuidString($uuidString);

        $this->assertTrue(
            $originalUuid1->equals(
                Uuid::fromString($todoId->asString())
            )
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function shouldCreateFromUuid()
    {
        $originalUuid1 = Uuid::uuid1();

        $todoId = TodoId::fromUuid($originalUuid1);

        $this->assertTrue(
            $originalUuid1->equals(
                $todoId->asUuid()
            )
        );
    }
}
