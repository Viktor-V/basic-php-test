<?php

declare(strict_types=1);

namespace App\Service\Export\ItemCategories;

use App\Service\Export\ExporterInterface;
use App\Service\Storage\StorageInterface;

class ExelExporter implements ExporterInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function export(array $data): void
    {
        $excel = implode("\t", ['Id', 'Name']) . "\n";

        foreach ($data as $row) {
            $excel .= implode("\t", array_values($row)) . "\n";
        }

        $this->storage->save('export.xls', $excel);
    }
}