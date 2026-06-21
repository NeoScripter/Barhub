<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;

final class IntegrationHandler
{

    private $token = "token";

    public function get_token()
    {
        // if (Cache::has('api_token')) {
        //     return Cache::get('api_token');
        // }

        $response = Http::asForm()->post('https://api-integration.eventicious.ru/connect/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => 'cl-12105-7adba68d-9a33-4c90-bd3d-d93ff2e91b77',
            'client_secret' => 'cs-12105-37d7c43d-f71a-4689-8cbe-e30d5bd90645',
        ]);

        if (! $response->successful()) {
            // TODO: handle error
        }

        $body = $response->body();
        if (! json_validate($body)) {
            // TODO: handle error
        }
        $body = json_decode($body);

        $body = $this->class_to_array($body);

        if (! array_key_exists('access_token', $body)) {
            // TODO: handle error
        }
        $token = $body['access_token'];

        return $token;
    }

    public function create_company()
    {
        $company = Company::first();
        $url = 'https://api-integration.eventicious.ru/api/external/v2/expo/create';

        $response = Http::withToken($this->token)->post($url, $this->company_resource($company));

        if (! $response->ok()) {
            $body = $response->body();
            if (! json_validate($body)) {
                // TODO: handle error
            }
            $body = json_decode($body);
            $body = $this->class_to_array($body);
            if (! array_key_exists('title', $body)) {
                // TODO: handle error
            }
            $error = $body['title'];
            dd($error);

            return ['error' => $error];
        }

        return ['success' => 'Exponent successfully added'];
    }

    public function update_company()
    {
        $company = Company::first();
        $url = 'https://api-integration.eventicious.ru/api/external/v2/expo/create';

        $response = Http::withToken($this->token)->post($url, $this->company_resource($company));

        if (! $response->ok()) {
            $body = $response->body();
            if (! json_validate($body)) {
                // TODO: handle error
            }
            $body = json_decode($body);
            $body = $this->class_to_array($body);
            if (! array_key_exists('title', $body)) {
                // TODO: handle error
            }
            $error = $body['title'];
            dd($error);

            return ['error' => $error];
        }

        return ['success' => 'Exponent successfully added'];
    }

    private function company_resource(Company $company)
    {
        return [
            'id' => $company->id,
            'language' => "ru-RU",
            'name' => $company->public_name,
            'address' => $company->stand_code,
            'site' => $company->site_url,
            'email' => $company->email,
            'phone' => $company->phone,
            'details' => $company->description,
            'externalImagePath' => $company->logo ? $company->logo->webp : url('placeholder.webp'),
        ];
    }

    private function class_to_array(stdClass $class)
    {
        if (! $class instanceof stdClass) {
            throw new Exception('The provided variable is not an instance of stdClass');
        }
        return json_decode(json_encode($class), true);
    }
}
