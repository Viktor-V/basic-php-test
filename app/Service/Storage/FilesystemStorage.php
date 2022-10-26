<?php

declare(strict_types=1);

namespace App\Service\Storage;

class FilesystemStorage implements StorageInterface
{
    public function save(string $filename, $content): void
    {
        file_put_contents(__DIR__ . '/../../export/' . $filename, $content);
    }
}