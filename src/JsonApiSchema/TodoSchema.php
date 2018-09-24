<?php

declare(strict_types=1);

namespace App\JsonApiSchema;

use App\Serializer\SerializesDateTimesTrait;
use Neomerx\JsonApi\Schema\BaseSchema;

class TodoSchema extends BaseSchema
{
    use SerializesDateTimesTrait;

    protected $resourceType = 'todos';

    /**
     * @param \App\Entity\Todo $resource
     * @return null|string
     */
    public function getId($resource): ?string
    {
        return $resource->getId()->asString();
    }

    /**
     * @param \App\Entity\Todo $todo
     * @param array|null $fieldKeysFilter
     * @return array|null
     */
    public function getAttributes($todo, array $fieldKeysFilter = null): ?array
    {
        return [
            'title'       => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'created-at'  => $this->serializeDate($todo->getCreatedAt()),
            'updated-at'  => $this->serializeDate($todo->getUpdatedAt()),
        ];
    }
}