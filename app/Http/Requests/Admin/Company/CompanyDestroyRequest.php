<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;

final class CompanyDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $exhibition = $this->company->exhibition;
        return $this->user()->can('view', $exhibition);
    }
}
