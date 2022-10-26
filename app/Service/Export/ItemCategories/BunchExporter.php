<?php

declare(strict_types=1);

namespace App\Service\Export\ItemCategories;

use App\Service\Export\ExporterInterface;

class BunchExporter implements ExporterInterface
{
    /**
     * @var ExporterInterface[] $exporters
     */
    private iterable $exporters;

    public function __construct(
        iterable $exporters
    ) {
        $this->exporters = $exporters;
    }

    public function export(array $data): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->export($data);
        }
    }
}