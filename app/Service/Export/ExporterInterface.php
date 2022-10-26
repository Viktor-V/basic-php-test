<?php

declare(strict_types=1);

namespace App\Service\Export;

interface ExporterInterface
{
    public function export(array $data): void;
}