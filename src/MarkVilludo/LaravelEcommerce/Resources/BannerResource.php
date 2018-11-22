<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'original_path' => url('/storage/banners/'.$this->file_name),
            'medium_path' => url('/storage/banners/medium/'.$this->file_name),
            'small_path' => url('/storage/banners/small/'.$this->file_name),
            'xsmall_path' => url('/storage/banners/xsmall/'.$this->file_name)
        ];
    }
}
