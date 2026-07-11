<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Company;
use App\Services\Integration\HtmlSanitizer;

class CompanyResource
{
    public static function make(Company $company): array
    {
        $payload = [
            'id'       => $company->id,
            'language' => 'ru-RU',
            'name'     => $company->public_name,
            'address'  => $company->stand_code,
            'site'     => $company->site_url,
            'email'    => $company->email,
            'phone'    => $company->phone,
            'details'  => self::details($company),
        ];

        // Без логотипа поле не отправляем — карточка остаётся без изображения.
        if ($company->logo) {
            $payload['externalImagePath'] = route('integration.image', $company->logo);
        }

        return $payload;
    }

    /**
     * У карточки экспонента в Eventicious нет полей под соцсети,
     * поэтому Instagram/Telegram добавляем ссылками в конец описания.
     */
    private static function details(Company $company): ?string
    {
        $details = HtmlSanitizer::clean($company->description) ?? '';

        $socials = array_filter([
            $company->instagram ? '<a href="' . e($company->instagram) . '">Instagram</a>' : null,
            $company->telegram ? '<a href="' . e($company->telegram) . '">Telegram</a>' : null,
        ]);

        if ($socials !== []) {
            $details .= '<p>' . implode(' | ', $socials) . '</p>';
        }

        return $details !== '' ? $details : null;
    }
}
