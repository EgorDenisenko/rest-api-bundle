<?php

namespace Onixcat\Bundle\RestApiBundle;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * Wrap Symfony serializer with extra features
 *
 * @package Onixcat\Bundle\RestApiBundle
 */
class Serializer implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = [])
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, array $context = [])
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * @param ResponseDocument $responseDocument
     * @param array $groups
     *
     * @return string
     */
    public function toJson(ResponseDocument $responseDocument, array $groups = []): string
    {
        return $this->serializer->serialize(
            $responseDocument,
            'json',
            [
                'groups' => $groups ?: ['default'],
            ]
        );
    }
}
