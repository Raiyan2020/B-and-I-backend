<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SubscriptionPackage
 */
class SubscriptionPackageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price_monthly' => __('apis.price_per_month', ['price' => (float) $this->price_monthly]),
            'can_register' => $user !== null,
            'is_subscribed' => $user !== null
                && (int) ($user->subscription_package_id ?? 0) === (int) $this->id,
        ];
    }
}
