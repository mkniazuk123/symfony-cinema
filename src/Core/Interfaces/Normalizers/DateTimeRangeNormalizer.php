<?php

namespace App\Core\Interfaces\Normalizers;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DateTimeRangeNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface, DenormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use DenormalizerAwareTrait;

    public function getSupportedTypes(?string $format): array
    {
        return [DateTimeRange::class => true];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof DateTimeRange;
    }

    /**
     * @param DateTimeRange $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array
    {
        return [
            'start' => $this->normalizer->normalize($data->start, $format, $context),
            'end' => $this->normalizer->normalize($data->end, $format, $context),
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return DateTimeRange::class === $type;
    }

    /**
     * @param array{start: string, end: string} $data
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): DateTimeRange
    {
        return new DateTimeRange(
            start: $this->denormalizer->denormalize($data['start'], DateTime::class, $format, $context),
            end: $this->denormalizer->denormalize($data['end'], DateTime::class, $format, $context),
        );
    }
}
