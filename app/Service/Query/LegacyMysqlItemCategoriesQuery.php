<?php

declare(strict_types=1);

namespace App\Service\Query;

class LegacyMysqlItemCategoriesQuery extends AbstractLegacyMysql implements QueryInterface
{
    public function fetchAll(): array
    {
        return $this->sql->get('SELECT * FROM item_categories');
    }
}