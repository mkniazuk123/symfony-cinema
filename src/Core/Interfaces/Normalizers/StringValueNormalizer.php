<?php

namespace App\Core\Interfaces\Normalizers;

use App\Core\Domain\StringValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StringValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [StringValue::class => true];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof StringValue;
    }

    /**
     * @param StringValue $object
     */
    public function normalize($object, ?string $format = null, array $context = []): string
    {
        return $object->value();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, StringValue::class);
    }

    /**
     * @template T of StringValue
     *
     * @param string          $data
     * @param class-string<T> $type
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): StringValue
    {
        return new $type($data);
    }
}
