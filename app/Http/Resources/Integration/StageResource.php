<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Stage;
use Illuminate\Support\Facades\DB;

class StageResource
{
    public static function make(Stage $stage): array
    {
        return [
            'id'            => $stage->id,
            'name'          => $stage->name,
            'position'      => $stage->id,
        ];
    }
}
