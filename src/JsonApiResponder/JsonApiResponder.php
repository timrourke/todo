<?php

declare(strict_types=1);

namespace App\JsonApiResponder;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\Parameters\EncodingParameters;
use Neomerx\JsonApi\Factories\Factory;
use Neomerx\JsonApi\Http\BaseResponses;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Symfony\Component\HttpFoundation\Response;

class JsonApiResponder extends BaseResponses
{
    const HTTP_NO_CONTENT = 204;

    /**
     * @var \Neomerx\JsonApi\Contracts\Schema\ContainerInterface
     */
    private $container;

    /**
     * @var \Neomerx\JsonApi\Encoder\Encoder
     */
    private $encoder;

    /**
     * @var \Neomerx\JsonApi\Factories\Factory
     */
    private $factory;

    public function __construct(Factory $factory, array $schemas)
    {
        $this->factory = $factory;

        $this->container = $factory->createContainer($schemas);

        $this->encoder = new Encoder($factory, $this->container);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getNoContentResponse(): Response
    {
        return $this->getCodeResponse(self::HTTP_NO_CONTENT);
    }

    protected function createResponse(?string $content, int $statusCode, array $headers)
    {
        return new Response($content, $statusCode, $headers);
    }

    protected function getEncoder(): EncoderInterface
    {
        return $this->encoder;
    }

    protected function getUrlPrefix(): ?string
    {
        return '';
    }

    protected function getEncodingParameters(): ?EncodingParametersInterface
    {
        return new EncodingParameters();
    }

    protected function getSchemaContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    protected function getMediaType(): MediaTypeInterface
    {
        return new MediaType('application', 'vnd.api+json');
    }
}