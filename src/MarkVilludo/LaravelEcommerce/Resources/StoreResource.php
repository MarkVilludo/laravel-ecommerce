<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'name' => $this->name,
            'complete_address' => $this->complete_address,
            'province'=> new ProvinceResource($this->province),
            'country'=> new CountryResource($this->country),
            'city'=> new CityResource($this->city)
        ];
    }
}
