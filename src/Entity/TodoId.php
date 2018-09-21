<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TodoId
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public static function fromUuidString(string $uuidString): TodoId
    {
        return new self(Uuid::fromString($uuidString));
    }

    public static function fromUuid(UuidInterface $uuid): TodoId
    {
        return new self($uuid);
    }

    public function asString(): string
    {
        return $this->id->toString();
    }

    public function asUuid(): UuidInterface
    {
        return $this->id;
    }
}