<?php

namespace App\Services;

use Costa\DomainPackage\UseCase\Interfaces\FileStorageInterface;

class FileStorage implements FileStorageInterface
{
    public function store(string $path, array $file): string
    {
        //
    }

    public function remove(string $path): bool
    {
        //
    }
}
