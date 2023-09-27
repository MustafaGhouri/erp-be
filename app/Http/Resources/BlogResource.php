<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            "slug" => $this->slug,
            "title" => $this->title,
            "excerpt" => $this->excerpt,
            "thumbnail" => asset('uploads/blogs/') . "/" . $this->image,
            "content" => json_decode($this->content) ?? $this->content,
            "is_published" => $this->is_published,
            "views" => $this->views,
            "comments" => $this?->comments,
            "likes" => $this->likes,
            "created_at" => $this->created_at,
            "category" => [
                "id" => $this?->category?->id,
                "name" => $this?->category?->name,
            ],
            "author" => [
                "id" => $this?->author?->id,
                "first_name" => $this?->author?->first_name,
                "last_name" => $this?->author?->last_name,
                "email" => $this?->author?->email,
                "image" => asset('uploads/users/') . "/" . $this?->author?->image,
                "phone_number" => $this?->author?->phone_number,
            ],
            "tags" => $this->getTags($this?->tags),
        ];
    }

    static public function getTags($tags)
    {
        $array = collect($tags)->map(function ($item) {
            return (object) [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        })->toArray();

        return $array;
    }
}
