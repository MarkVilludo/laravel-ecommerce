<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'file_name' => $this->file_name,
            'path' => url($this->path.'/'.$this->file_name),
            'content' => $this->content,
            'content_web' => $this->content_web,
        ];
    }
}
