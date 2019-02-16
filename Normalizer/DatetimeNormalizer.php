<?php

namespace Onixcat\Bundle\RestApiBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\{
    NormalizerInterface, DenormalizerInterface
};
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class DatetimeNormalizer
 * @package Onixcat\SyliusBundle\CartBundle\Normalizer
 */
class DatetimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @param \DateTime $object
     * @param null $format
     * @param array $context
     * @return int
     */
    public function normalize($object, $format = null, array $context = []): int
    {
        return $object->getTimestamp();
    }

    /**
     * @param mixed $data
     * @param null $format
     * @return bool
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof \DateTimeInterface;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        try {
            return \DateTime::class === $class ? (new \DateTime)->setTimestamp($data) : (new \DateTimeImmutable)->setTimestamp($data);
        } catch (\Exception $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        $supportedTypes = [
            \DateTimeInterface::class => true,
            \DateTimeImmutable::class => true,
            \DateTime::class => true,
        ];

        return isset($supportedTypes[$type]);
    }
}
