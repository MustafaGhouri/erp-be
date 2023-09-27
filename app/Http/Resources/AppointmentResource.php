<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "appointment_date" => $this->appointment_date,
            "appointment_start_time" => $this->appointment_start_time,
            "appointment_end_time" => $this->appointment_end_time,
            "status" => $this->status,
            "status_text" => $this->status === 0 ? "pending" : "approved",
            "client" => [
                "id" => $this?->client?->id,
                "first_name" => $this?->client?->first_name,
                "last_name" => $this?->client?->last_name,
            ],
            "lawyer" => [
                "id" => $this?->lawyer?->id,
                "first_name" => $this?->lawyer?->first_name,
                "last_name" => $this?->lawyer?->last_name,
            ],
            "created_at" => $this->created_at,
        ];
    }
}
