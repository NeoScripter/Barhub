<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'language' => "ru-RU",
            'name' => $this->public_name,
            'address' => $this->stand_code,
            'site' => $this->site_url,
            'email' => $this->email,
            'phone' => $this->phone,
            'details' => $this->description,
            'externalImagePath' => $this->logo ? $this->logo->webp : url('placeholder.webp'),
        ];
    }
}
