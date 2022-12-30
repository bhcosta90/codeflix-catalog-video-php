<?php

namespace App\Services;

use Costa\DomainPackage\UseCase\Interfaces\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorage implements FileStorageInterface
{
    public function store(string $path, array $file): string
    {
        $contents = $this->convertFileToLaravelFile($file);
        return Storage::put($path, $contents);
    }

    public function delete(string $path): bool
    {
        return (bool) Storage::delete($path);
    }

    protected function convertFileToLaravelFile(array $file): UploadedFile
    {
        return new UploadedFile(
            path: $file['tmp_name'],
            originalName: $file['name'],
            mimeType: $file['type'],
            error: $file['error'],
        );
    }
}
