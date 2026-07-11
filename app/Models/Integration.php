<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $fillable = ['status'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
}
