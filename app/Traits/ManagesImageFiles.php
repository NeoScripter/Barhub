<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\ImageResizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

trait ManagesImageFiles
{
    /**
     * Attach image to a model
     */
    public static function attachToModel(
        $model,
        UploadedFile $file,
        string $type,
        string $folder,
        int $baseWidth,
        string $alt = ''
    ): self {
        $resizer = app(ImageResizer::class);
        $paths = $resizer->handleImage($file, $folder, $baseWidth);

        return $model->images()->create([
            'webp3x' => $paths['webp3x'],
            'webp2x' => $paths['webp2x'],
            'webp' => $paths['webp'],
            'avif3x' => $paths['avif3x'],
            'avif2x' => $paths['avif2x'],
            'avif' => $paths['avif'],
            'tiny' => $paths['tiny'],
            'alt' => $alt,
            'type' => $type,
        ]);
    }

    /**
     * Delete all image files from storage
     */
    public function deleteFiles(): void
    {
        $paths = [
            $this->webp3x,
            $this->webp2x,
            $this->webp,
            $this->avif3x,
            $this->avif2x,
            $this->avif,
            $this->tiny,
        ];

        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Update image: alt only or replace image files
     */
    public function updateImage(
        ?UploadedFile $file = null,
        ?string $alt = null,
        ?string $folder = null,
        ?int $baseWidth = null
    ): self {
        // Update alt text if provided
        if ($alt !== null) {
            $this->alt = $alt;
        }

        // Replace image files if new file uploaded
        if ($file instanceof UploadedFile) {
            throw_if($folder === null || $baseWidth === null, InvalidArgumentException::class, 'Folder and baseWidth are required when updating image file');

            // Delete old files
            $this->deleteFiles();

            // Generate new files
            $resizer = app(ImageResizer::class);
            $paths = $resizer->handleImage($file, $folder, $baseWidth);

            // Update paths
            $this->webp3x = $paths['webp3x'];
            $this->webp2x = $paths['webp2x'];
            $this->webp = $paths['webp'];
            $this->avif3x = $paths['avif3x'];
            $this->avif2x = $paths['avif2x'];
            $this->avif = $paths['avif'];
            $this->tiny = $paths['tiny'];
        }

        $this->save();

        return $this;
    }

    /**
     * Boot the trait
     */
    protected static function bootManagesImageFiles(): void
    {
        // Automatically delete files when image model is deleted
        static::deleting(function ($image): void {
            $image->deleteFiles();
        });
    }
}
