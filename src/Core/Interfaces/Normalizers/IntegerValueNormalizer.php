<?php

namespace App\Core\Interfaces\Normalizers;

use App\Core\Domain\IntegerValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IntegerValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [IntegerValue::class => true];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof IntegerValue;
    }

    /**
     * @param IntegerValue $object
     */
    public function normalize($object, ?string $format = null, array $context = []): int
    {
        return $object->value();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, IntegerValue::class);
    }

    /**
     * @template T of IntegerValue
     *
     * @param int             $data
     * @param class-string<T> $type
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): IntegerValue
    {
        return new $type($data);
    }
}
