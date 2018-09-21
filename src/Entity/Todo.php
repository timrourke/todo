<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TodoRepository")
 */
class Todo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private $updatedAt;

    public function __construct(
        TodoId $todoId,
        string $title,
        string $description,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $todoId->asInt();
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \App\Entity\TodoId
     */
    public function getId(): TodoId
    {
        return TodoId::fromInteger($this->id);
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

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param string $newTitle
     * @throws \Exception
     */
    public function changeTitle(string $newTitle): void
    {
        $this->title = $newTitle;

        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @param string $newDescription
     * @throws \Exception
     */
    public function changeDescription(string $newDescription): void
    {
        $this->description = $newDescription;

        $this->updatedAt = new DateTimeImmutable();
    }
}
