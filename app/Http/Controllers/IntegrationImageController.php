<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Отдаёт картинку в формате JPEG для Eventicious: их API принимает только
 * image/jpeg и image/png, а сайт хранит webp/avif. Конвертируем по требованию
 * и кэшируем результат на диске.
 */
final class IntegrationImageController extends Controller
{
    public function show(Image $image): BinaryFileResponse
    {
        $disk = Storage::disk('public');

        abort_unless($image->webp && $disk->exists($image->webp), 404);

        $jpgPath = "integration-jpg/{$image->id}.jpg";
        $sourcePath = $disk->path($image->webp);

        $stale = !$disk->exists($jpgPath)
            || filemtime($sourcePath) > filemtime($disk->path($jpgPath));

        if ($stale) {
            $disk->makeDirectory('integration-jpg');

            $manager = new ImageManager(new Driver());
            $manager->read($sourcePath)
                ->toJpeg(85)
                ->save($disk->path($jpgPath));
        }

        return response()->file($disk->path($jpgPath), [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
