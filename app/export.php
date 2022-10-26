<?php

require_once __DIR__ . '/autoload.php';

echo "Export...\n";

$storage = new App\Service\Storage\FilesystemStorage();

$exporter = new \App\Service\Export\ItemCategories\BunchExporter([
    new \App\Service\Export\ItemCategories\JsonExporter($storage),
    new \App\Service\Export\ItemCategories\ExelExporter($storage)
]);

$itemCategoriesService = new \App\Service\ItemCategories\ItemCategoriesService(
    new \App\Service\Query\LegacyMysqlItemCategoriesQuery()
);

$exporter->export($itemCategoriesService->getItemCategoriesWithIdAndName());

echo "Export finished.\n";