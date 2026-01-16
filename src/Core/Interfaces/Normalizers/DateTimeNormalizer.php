<?php

namespace App\Core\Interfaces\Normalizers;

use App\Core\Domain\DateTime;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(?string $format): array
    {
        return [DateTime::class => true];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof DateTime;
    }

    /**
     * @param DateTime $data
     */
    public function normalize($data, ?string $format = null, array $context = []): string
    {
        return (string) $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return DateTime::class === $type;
    }

    /**
     * @param string $data
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): DateTime
    {
        return DateTime::parse($data);
    }
}
