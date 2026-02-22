<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request): array
    {
        $type = class_basename($this->favoriteable_type);

        return [
            'id'        => $this->id,
            'type'      => strtolower($type),
            'entity_id' => $this->favoriteable_id,
            'entity'    => $this->whenLoaded('favoriteable', fn() => $this->buildEntity()),
            'added_at'  => $this->created_at->toDateTimeString(),
        ];
    }

    private function buildEntity(): array
    {
        $item = $this->favoriteable;
        if (! $item) return [];

        // مشترك بين كل الـ types
        return [
            'id'         => $item->id,
            'name'       => $item->name ?? $item->title ?? null,
            'main_image' => $item->main_image_url ?? null,
            'slug'       => $item->slug ?? null,
        ];
    }
}