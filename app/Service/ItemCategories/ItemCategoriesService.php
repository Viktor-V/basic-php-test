<?php

declare(strict_types=1);

namespace App\Service\ItemCategories;

use App\Service\Query\QueryInterface;

class ItemCategoriesService
{
    private QueryInterface $query;

    public function __construct(
        QueryInterface $query
    ) {
        $this->query = $query;
    }

    public function getItemCategoriesWithIdAndName(): array
    {
        $itemCategories = [];

        foreach ($this->query->fetchAll() as $item) {
            $itemCategories[] = [
                'id' => $item['ID'] ?? null,
                'name' => $item['name'] ?? null
            ];
        }

        return $itemCategories;
    }
}