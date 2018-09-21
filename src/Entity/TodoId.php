<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TodoId
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromInteger(int $id): TodoId
    {
        return new self($id);
    }

    public function asInt(): int
    {
        return $this->id;
    }
}