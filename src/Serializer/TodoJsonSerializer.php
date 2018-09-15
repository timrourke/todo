<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Todo;
use DateTime;

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
            'createdAt'   => $todo->getCreatedAt()->format(DateTime::ATOM),
            'updatedAt'   => $todo->getUpdatedAt()->format(DateTime::ATOM),
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