<?php

include('sql.php');
include('DataGenerator.php');

$a = new StdClass();
$a->conf = ['sql' => [[
        'HOST' => 'mysql',
        'DB_LGN'=>'root',
        'DB_PSW' => 'myPassw1',
        'DB' => 'erp',
    ]]];

$a->sql = new sql();
$a->sql->connect();

$_generator = new DataGenerator();
$_generator->generateSubs(0, 8, 15);
