<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\JournalSliderResource;

class JournalResource extends JsonResource
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
            'featured_image' => url('/storage/journals/'.$this->file_name),
            'content' => $this->content,
            'category' =>$this->category->name,
            'sliders' => JournalSliderResource::collection($this->sliders),
            'journal_category_id' =>$this->category->id,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
