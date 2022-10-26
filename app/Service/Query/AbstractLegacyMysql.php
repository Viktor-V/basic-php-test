<?php

declare(strict_types=1);

namespace App\Service\Query;

use sql;
use stdClass;

abstract class AbstractLegacyMysql
{
    protected sql $sql;

    public function __construct()
    {
        global $a;

        $a = new stdClass();
        $a->conf = ['sql' => [[
            'HOST' => 'mysql',
            'DB_LGN'=>'root',
            'DB_PSW' => 'myPassw1',
            'DB' => 'erp',
        ]]];

        $this->sql = new sql();
        $this->sql->connect();
    }
}