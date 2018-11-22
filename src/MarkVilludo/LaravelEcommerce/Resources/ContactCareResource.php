<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactCareResource extends JsonResource
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
            'contact_number' => $this->contact_number,
            'email' => $this->email,
            'shipping_concern_email' => $this->shipping_concern_email,
            'pr_media_inquiry_email' => $this->pr_media_inquiry_email,
            'partnership_business_inquery_email' => $this->partnership_business_inquery_email
        ];
    }
}
