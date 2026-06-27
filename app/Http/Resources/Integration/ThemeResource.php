<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Theme;

class ThemeResource
{
    public static function make(Theme $theme): array
    {
        return [
            'id'                => $theme->id,
            'name'              => $theme->name,
            'color'             => $theme->color_hex,
            'visibilityFlag'    => 1,
        ];
    }
}
