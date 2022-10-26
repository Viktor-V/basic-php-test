<?php

declare(strict_types=1);

namespace App\Service\Query;

interface QueryInterface
{
    public function fetchAll(): array;
}