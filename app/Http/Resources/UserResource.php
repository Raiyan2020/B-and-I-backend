<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $token;
    public function setToken($token)
    {
        $this->token = $token;
    }
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'role' => $this->role,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'email_verified' => (bool)$this->email_verified_at,
            'subscription_plan' => $this->subscription_plan,
            'token' => $this->token ?? null,
        ];

        if ($this->role === 'investor') {
            $data = array_merge($data, [
                'investor_type' => $this->investor_type,
                'investor_sector' => $this->investor_sector,
                'investor_capital' => $this->investor_capital,
                'investment_count' => $this->investment_count,
                'investor_experience' => $this->investor_experience,
            ]);
        }

        if ($this->role === 'advertiser') {
            $data = array_merge($data, [
                'company_name' => $this->company_name,
                'company_license_url' => $this->company_license_url,
                'license_number' => $this->license_number,
            ]);
        }

        return $data;
    }
}
