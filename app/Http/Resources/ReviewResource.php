<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'user'          => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'rating'        => $this->rating,
            'title'         => $this->title,
            'body'          => $this->body,
            'photos'        => $this->photos_urls,
            'helpful_votes' => $this->helpful_votes,
            'status'        => $this->status,
            'created_at'    => $this->created_at->toDateTimeString(),
        ];
    }
}
