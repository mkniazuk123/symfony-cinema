<?php

namespace App\Core\Application;

interface QueryBus
{
    /**
     * @template T of mixed
     *
     * @param Query<T> $query
     *
     * @return T
     */
    public function query(Query $query): mixed;
}
