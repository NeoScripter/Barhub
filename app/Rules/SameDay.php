<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SameDay implements ValidationRule
{
    public function __construct(protected string $otherDate) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Carbon::parse($value)->toDateString() !== Carbon::parse($this->otherDate)->toDateString()) {
            $fail("{$attribute} должна быть в тот же день что и дата начала.");
        }
    }
}
