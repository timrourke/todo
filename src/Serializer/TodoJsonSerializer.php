<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Todo;
use DateTimeImmutable;

class TodoJsonSerializer
{
    /**
     * @param \App\Entity\Todo $todo
     * @return array
     */
    public function serializeOne(Todo $todo): array
    {
        return [
            'id'          => $todo->getId(),
            'title'       => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'createdAt'   => $todo->getCreatedAt()->format(DateTimeImmutable::ATOM),
            'updatedAt'   => $todo->getUpdatedAt()->format(DateTimeImmutable::ATOM),
        ];
    }

    /**
     * @param \App\Entity\Todo[] $todos
     * @return array
     */
    public function serializeMany(array $todos): array
    {
        return array_map([$this, 'serializeOne'], $todos);
    }
}