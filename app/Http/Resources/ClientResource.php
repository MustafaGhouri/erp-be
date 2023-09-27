<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this?->image;

        if ($image && $this?->provider === "nbundl") {
            $imagePath = asset('uploads/users/') . "/" . $image;
        } else if ($image == null || $image == '') {
            $imagePath = asset('uploads/users/') . "/" . "default.png";
        } else {
            $imagePath = $image;
        }

        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "image" => $imagePath,
            "phone_number" => $this->phone_number,
            "address" => $this->address,
            "country" => [
                "id" => $this?->country?->id,
                "name" => $this?->country?->name,
            ],
            "state" => [
                "id" => $this?->state?->id,
                "name" => $this?->state?->name,
            ],
            "city" => $this->city,
            "zip_code" => $this->zip_code,
            "terms_of_service" => $this->terms_of_service,
            "email_verification_status" => $this->email_verified_at ? "verified" : "not verified",
            "email_verified_at" => $this->email_verified_at,
            "status" => $this->status,
            "is_online" => $this->is_online,
            "dob" => $this->dob,
            "balance" => $this->balance,
            "iban" => $this->iban,
            "created_at" => $this->created_at,
        ];
    }
}
