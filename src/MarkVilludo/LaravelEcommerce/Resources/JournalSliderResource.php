<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalSliderResource extends JsonResource
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
            'id' => $this->id,
            'journal_id' => $this->journal_id,
            'file_name' => $this->file_name,
            'original_path' => url('/storage/journals/'.$this->file_name),
            'medium_path' => url('/storage/journals/medium/'.$this->file_name),
            'small_path' => url('/storage/journals/small/'.$this->file_name),
            'xsmall_path' => url('/storage/journals/xsmall/'.$this->file_name)
        ];
    }
}
