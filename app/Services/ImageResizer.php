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
            Log::info('Starting image processing', [
                'original_name' => $file->getClientOriginalName(),
                'folder' => $folder,
                'base_width' => $baseWidth,
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType()
            ]);

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalName).'-'.uniqid();

            Log::info('Generated filename', ['filename' => $filename]);

            // Check if Imagick is loaded
            if (!extension_loaded('imagick')) {
                Log::error('Imagick extension is not loaded');
                throw new \Exception('Imagick extension is not loaded');
            }

            Log::info('Imagick extension is loaded');

            $manager = new ImageManager(new Driver());

            // Check if we can read the image
            try {
                $image = $manager->read($file);
                Log::info('Successfully read image', [
                    'width' => $image->width(),
                    'height' => $image->height()
                ]);
            } catch (RuntimeException $e) {
                Log::error('Failed to read image', ['error' => $e->getMessage()]);
                throw $e;
            }

            // Check if directory can be created
            try {
                $directoryExists = Storage::disk('public')->makeDirectory("uploads/{$folder}");
                Log::info('Directory creation result', [
                    'directory' => "uploads/{$folder}",
                    'exists' => $directoryExists
                ]);
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
                        Log::info("Processing {$formatKey} at width {$width}", [
                            'density_suffix' => $densitySuffix
                        ]);

                        $scaled = $image->scaleDown(width: $width);
                        Log::info('Image scaled successfully', [
                            'new_width' => $scaled->width(),
                            'new_height' => $scaled->height()
                        ]);

                        $encoded = $scaled->{$formatConfig['method']}($formatConfig['quality']);
                        Log::info('Image encoded successfully', [
                            'method' => $formatConfig['method'],
                            'encoded_size' => strlen((string) $encoded)
                        ]);

                        $columnName = "{$formatKey}{$densitySuffix}";
                        $path = "uploads/{$folder}/{$filename}-{$columnName}.{$formatKey}";

                        $result = Storage::disk('public')->put($path, (string) $encoded);

                        Log::info('File save result', [
                            'path' => $path,
                            'result' => $result,
                            'full_path' => Storage::disk('public')->path($path)
                        ]);

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
                Log::info('Processing tiny image');
                $tiny = $image->scaleDown(width: 20)->toWebp(80);
                $tinyPath = "uploads/{$folder}/{$filename}-tiny.webp";

                $result = Storage::disk('public')->put($tinyPath, (string) $tiny);

                Log::info('Tiny image save result', [
                    'path' => $tinyPath,
                    'result' => $result
                ]);

                $paths['tiny'] = Storage::disk('public')->url($tinyPath);
            } catch (\Exception $e) {
                Log::error('Failed to process tiny image', ['error' => $e->getMessage()]);
                throw $e;
            }

            Log::info('Image processing completed successfully', ['paths' => array_keys($paths)]);
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
