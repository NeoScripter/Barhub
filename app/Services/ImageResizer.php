<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Exceptions\RuntimeException;

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
        try {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalName).'-'.uniqid();

            if (!extension_loaded('imagick')) {
                Log::error('Imagick extension is not loaded');
                throw new \Exception('Imagick extension is not loaded');
            }

            $manager = new ImageManager(new Driver());

            // Check if we can read the image
            try {
                $image = $manager->read($file);
            } catch (RuntimeException $e) {
                Log::error('Failed to read image', ['error' => $e->getMessage()]);
                throw $e;
            }

            // Check if directory can be created
            try {
                Storage::disk('public')->makeDirectory("uploads/{$folder}");
            } catch (\Exception $e) {
                Log::error('Failed to create directory', [
                    'directory' => "uploads/{$folder}",
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }

            $paths = [];

            foreach ($this->densities as $densitySuffix => $densityMultiplier) {
                $width = $baseWidth * $densityMultiplier;

                foreach ($this->formats as $formatKey => $formatConfig) {
                    try {
                        $scaled = $image->scaleDown(width: $width);

                        $encoded = $scaled->{$formatConfig['method']}($formatConfig['quality']);

                        $columnName = "{$formatKey}{$densitySuffix}";
                        $path = "uploads/{$folder}/{$filename}-{$columnName}.{$formatKey}";

                        $result = Storage::disk('public')->put($path, (string) $encoded);

                        if (!$result) {
                            throw new \Exception("Failed to save file: {$path}");
                        }

                        $paths[$columnName] = Storage::disk('public')->url($path);

                    } catch (\Exception $e) {
                        Log::error("Failed to process {$formatKey} at width {$width}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
            }

            // Process tiny image
            try {
                $tiny = $image->scaleDown(width: 20)->toWebp(80);
                $tinyPath = "uploads/{$folder}/{$filename}-tiny.webp";

                $result = Storage::disk('public')->put($tinyPath, (string) $tiny);

                $paths['tiny'] = Storage::disk('public')->url($tinyPath);
            } catch (\Exception $e) {
                Log::error('Failed to process tiny image', ['error' => $e->getMessage()]);
                throw $e;
            }

            return $paths;

        } catch (\Exception $e) {
            Log::error('Image processing failed completely', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
