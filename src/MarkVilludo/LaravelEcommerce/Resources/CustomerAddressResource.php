<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CityResource;

class CustomerAddressResource extends Resource
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
            'id'=> $this->id,
            'first_name'=> $this->first_name,
            'last_name'=> $this->last_name,
            'complete_address'=> $this->complete_address,
            'barangay'=> $this->barangay,
            'default_shipping'=> $this->default_shipping,
            'default_billing'=> $this->default_billing,
            'province'=> new ProvinceResource($this->province),
            'country'=> new CountryResource($this->country),
            'city'=> new CityResource($this->city),
            'zip_code'=> $this->zip_code,
            'mobile_number'=> $this->mobile_number,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
