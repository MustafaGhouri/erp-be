<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()?->user();

        if ($user?->role?->name === "SuperAdmin" || $user?->role?->name === "Admin") {
            return [
                "id" => $this->id,
                "site_name" => $this->site_name,
                "site_email" => $this->site_email,
                "site_contact" => $this->site_contact,
                "site_logo" => $this->site_logo ? asset("uploads/website/") . '/' . $this->site_logo : null,
                "site_favicon" => $this->site_favicon ? asset("uploads/website/") . '/' . $this->site_favicon : null,
                "admin_commission_percent" => $this->admin_commission_percent,
                "gpt_key" => $this->gpt_key,
                "stripe_public_key" => $this->stripe_pk,
                "stripe_secret_key" => $this->stripe_sk,
                "encryption_key" => $this->encryption_key,
            ];
        } else {
            return [
                "id" => $this->id,
                "site_name" => $this->site_name,
                "site_email" => $this->site_email,
                "site_contact" => $this->site_contact,
                "site_logo" => $this->site_logo ? asset("uploads/website/") . '/' . $this->site_logo : null,
                "site_favicon" => $this->site_favicon ? asset("uploads/website/") . '/' . $this->site_favicon : null,
            ];
        }
    }
}
