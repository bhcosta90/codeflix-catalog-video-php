<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'year_launched' => $this->year_launched ?? $this->yearLaunched,
            'opened' => $this->opened,
            'rating' => $this->rating,
            'duration' => $this->duration,
            'created_at' => $this->created_at ?? $this->createdAt,
            'video' => $this->videoFile ?? '',
            'trailer' => $this->trailerFile ?? '',
            'banner' => $this->bannerFile ?? '',
            'thumb' => $this->thumbFile ?? '',
            'thumb_half' => $this->thumbHalfFile ?? '',
            'categories' => $this->categories,
            'genres' => $this->genres,
            'cast_members' => $this->cast_members ?? $this->castMembers ?? [],
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
