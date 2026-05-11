<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    private array $allowedMimes      = ['image/jpeg', 'image/png', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    private int   $maxSizeBytes      = 3 * 1024 * 1024;

    public function uploadImage(UploadedFile $file, string $folder): string
    {
        if ($file->getSize() > $this->maxSizeBytes) {
            throw new \Exception('Fichier trop volumineux. Maximum 3 Mo.');
        }

        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            throw new \Exception('Type de fichier non autorisé. Utilisez JPG, PNG ou WEBP.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \Exception('Extension non autorisée.');
        }

        $fileName = $folder . '/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));

        return $fileName;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}