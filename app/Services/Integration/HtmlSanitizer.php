<?php

declare(strict_types=1);

namespace App\Services\Integration;

final class HtmlSanitizer
{
    /**
     * Eventicious принимает ограниченный набор тегов в форматированных полях
     * (description, details): p, strong, em, span, div, a, ul, ol, li.
     */
    private const ALLOWED_TAGS = '<p><strong><em><span><div><a><ul><ol><li>';

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        return strip_tags($html, self::ALLOWED_TAGS);
    }
}
