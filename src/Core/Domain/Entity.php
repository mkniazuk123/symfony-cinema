<?php

namespace App\Core\Domain;

/**
 * @template T of object
 */
abstract class Entity
{
    /**
     * @param T $id
     */
    public function __construct(
        protected readonly object $id,
    ) {
    }

    /**
     * @return T
     */
    final public function getId(): object
    {
        return $this->id;
    }
}
