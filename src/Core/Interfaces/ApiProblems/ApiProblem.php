<?php

namespace App\Core\Interfaces\ApiProblems;

use App\Core\Domain\InvalidValueException;

abstract readonly class ApiProblem
{
    /**
     * @throws InvalidValueException
     */
    final public static function fromArray(array $array): static
    {
        $title = $array['title'] ?? throw new InvalidValueException('title is required');
        $type = $array['type'] ?? throw new InvalidValueException('type is required');
        $status = $array['status'] ?? null;
        $detail = $array['detail'] ?? null;
        $instance = $array['instance'] ?? null;

        unset($array['title'], $array['type'], $array['status'], $array['detail'], $array['instance']);
        $extensions = $array;

        return new static($type, $title, $status, $detail, $instance, $extensions);
    }

    final public function __construct(
        public string $type,
        public string $title,
        public ?int $status = null,
        public ?string $detail = null,
        public ?string $instance = null,
        public ?array $extensions = [],
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'title' => $this->title,
            'status' => $this->status,
            'detail' => $this->detail,
            'instance' => $this->instance,
        ];
        $data = array_filter($data, fn ($value) => null !== $value);

        return $data + $this->extensions;
    }
}
