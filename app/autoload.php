<?php

/**
 * Better to use composer autoload
 */
spl_autoload_register(static function ($class) {
    if ($class === 'sql') {
        require_once __DIR__ . '/sql.php';
        return;
    }

    require_once __DIR__ . str_replace(['\\', 'App'], ['/', ''], $class) . '.php';
});