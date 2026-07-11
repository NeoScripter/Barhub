<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Http\Resources\Integration\CompanyResource;
use App\Models\Company;

class CompanyIntegrationService extends BaseIntegrationService
{
    public function create(Company $company): bool
    {
        $response = $this->post('/api/external/v2/expo/create', CompanyResource::make($company));

        if (!$response->successful()) {
            $this->log_error('Не удалось создать экспонента', [
                'company_id' => $company->id,
                'error'      => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Экспонент создан', ['company_id' => $company->id]);

        return true;
    }

    public function update(Company $company): bool
    {
        $response = $this->put('/api/external/v2/expo/update/' . $company->id, CompanyResource::make($company));

        if (!$response->successful()) {
            $this->log_error('Не удалось отредактировать экспонента', [
                'company_id' => $company->id,
                'error'      => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Экспонент отредактирован', ['company_id' => $company->id]);

        return true;
    }

    /**
     * Обновить, а если записи в Eventicious ещё нет — создать.
     */
    public function sync(Company $company): bool
    {
        return $this->update($company) || $this->create($company);
    }

    public function destroy(int $companyId): bool
    {
        $response = $this->delete('/api/external/v2/expo/delete/' . $companyId);

        // 404 = в Eventicious её и так нет, считаем удаление успешным.
        if (!$response->successful() && $response->status() !== 404) {
            $this->log_error('Не удалось удалить экспонента', [
                'company_id' => $companyId,
                'error'      => $this->parse_error($response),
            ]);

            return false;
        }

        $this->log_info('Экспонент удален', ['company_id' => $companyId]);

        return true;
    }
}
