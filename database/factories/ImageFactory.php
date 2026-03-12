<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
final class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'webp3x' => '/storage/people/logo3x.webp',
            'webp2x' => '/storage/people/logo2x.webp',
            'webp' => '/storage/people/logo.webp',
            'avif3x' => '/storage/people/logo3x.avif',
            'avif2x' => '/storage/people/logo2x.avif',
            'avif' => '/storage/people/logo.avif',
            'tiny' => '/storage/people/logo-tiny.webp',
            'alt' => 'alt',
            'type' => 'image',
        ];
    }
}
