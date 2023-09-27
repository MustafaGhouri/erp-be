<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
            "comment_id" => $this->comment_id,
            "content" => $this->content,
            "created_at" => $this->created_at,
            "user" => new UserResource($this?->user),
        ];
    }
}
