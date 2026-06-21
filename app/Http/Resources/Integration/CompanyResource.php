<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Company;

class CompanyResource
{
    public static function make(Company $company): array
    {
        return [
            'id'                => $company->id,
            'language'          => 'ru-RU',
            'name'              => $company->public_name,
            'address'           => $company->stand_code,
            'site'              => $company->site_url,
            'email'             => $company->email,
            'phone'             => $company->phone,
            'details'           => $company->description,
            'externalImagePath' => $company->logo?->webp ?? url('placeholder.webp'),
        ];
    }
}
