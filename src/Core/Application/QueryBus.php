<?php

namespace App\Core\Application;

interface QueryBus
{
    public function query(Query $query): ?object;
}
