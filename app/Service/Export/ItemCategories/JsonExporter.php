<?php

declare(strict_types=1);

namespace App\Service\Export\ItemCategories;

use App\Service\Export\ExporterInterface;
use App\Service\Storage\StorageInterface;

class JsonExporter implements ExporterInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function export(array $data): void
    {
        $this->storage->save('export.json', json_encode($data, JSON_PRETTY_PRINT));
    }
}