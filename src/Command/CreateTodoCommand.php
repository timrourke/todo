<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TodoId;

class CreateTodoCommand
{
    /**
     * @var \App\Entity\TodoId
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    public function __construct(
        TodoId $id,
        string $title,
        string $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return \App\Entity\TodoId
     */
    public function getId(): TodoId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}