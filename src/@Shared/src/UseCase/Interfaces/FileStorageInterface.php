<?php

namespace Costa\DomainPackage\UseCase\Interfaces;

interface FileStorageInterface
{
    /**
     * @param string $path
     * @param array $_FILES[file]
     * @return string
     */
    public function store(string $path, array $file): string;

    /**
     * @param string $path
     * @return bool
     */
    public function remove(string $path): bool;
}
