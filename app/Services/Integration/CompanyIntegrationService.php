<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\Company;
use App\Http\Resources\Integration\CompanyResource;
use App\Services\Integration\BaseIntegrationService;

class CompanyIntegrationService extends BaseIntegrationService
{
    public function create(Company $company): void
    {
        $response = $this->post('/api/external/v2/expo/create', CompanyResource::make($company));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать экспонента', [
                'company_id' => $company->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Экспонент создан', ['company_id' => $company->id]);
    }

    public function update(Company $company): void
    {
        $response = $this->put('/api/external/v2/expo/update/' . $company->id, CompanyResource::make($company));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать экспонента', [
                'company_id' => $company->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Экспонент отредактирован', ['company_id' => $company->id]);
    }

    public function destroy(Company $company): void
    {
        $response = $this->delete('/api/external/v2/expo/delete/' . $company->id);

        if (!$response->successful()) {
            $this->log_error('Не удалось удалить экспонента', [
                'company_id' => $company->id,
                'error'      => $this->parse_error($response),
            ]);
            return;
        }

        $this->log_info('Экспонент удален', ['company_id' => $company->id]);
    }
}
