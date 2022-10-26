<?php

declare(strict_types=1);

namespace App\Service\Storage;

interface StorageInterface
{
    /**
     * @param string|resource $content
     */
    public function save(string $filename, $content): void;
}