<?php

declare(strict_types=1);

namespace App\Deserializer;

use App\Entity\Todo;

class TodoJsonDeserializer
{
    use DeserializesDateTimesTrait;

    public function deserializeOne(string $json): Todo
    {
        $data = json_decode($json, true);
        $todoFields = $data['todo'];

        $todo = new Todo();
        $todo->setTitle($todoFields['title']);
        $todo->setDescription($todoFields['description']);

        $updatedAt = $this->deserializeDate($todoFields['updatedAt']);
        if (!is_null($updatedAt)) {
            $todo->setUpdatedAt($updatedAt);
        }

        $createdAt = $this->deserializeDate($todoFields['createdAt']);
        if (!is_null($createdAt)) {
            $todo->setCreatedAt($createdAt);
        }

        return $todo;
    }
}