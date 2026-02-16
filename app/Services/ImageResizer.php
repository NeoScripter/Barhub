<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

final class ImageResizer
{
    private array $densities = [
        '3x' => 3,
        '2x' => 2,
        '' => 1,
    ];

    private array $formats = [
        'webp' => ['quality' => 80, 'method' => 'toWebp'],
        'avif' => ['quality' => 50, 'method' => 'toAvif'],
    ];

    public function handleImage(UploadedFile $file, string $folder, int $baseWidth): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Str::slug($originalName).'-'.uniqid();
        $manager = new ImageManager(new Driver());

        Storage::disk('public')->makeDirectory("uploads/{$folder}");

        $paths = [];

        foreach ($this->densities as $densitySuffix => $densityMultiplier) {
            $width = $baseWidth * $densityMultiplier;

            foreach ($this->formats as $formatKey => $formatConfig) {
                $image = $manager->read($file)->scaleDown(width: $width);
                $encoded = $image->{$formatConfig['method']}($formatConfig['quality']);

                $columnName = "{$formatKey}{$densitySuffix}";
                $path = "uploads/{$folder}/{$filename}-{$columnName}.{$formatKey}";

                Storage::disk('public')->put($path, (string) $encoded);

                $paths[$columnName] = $path;
            }
        }

        $tiny = $manager->read($file)->scaleDown(width: 20)->toWebp(80);
        $tinyPath = "uploads/{$folder}/{$filename}-tiny.webp";
        Storage::disk('public')->put($tinyPath, (string) $tiny);
        $paths['tiny'] = $tinyPath;

        return $paths;
    }
}
