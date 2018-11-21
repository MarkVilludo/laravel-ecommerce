<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customer_address';

    use SoftDeletes;

    //get billing or shipping addresses
    public function getAddresses($type)
    {
        return self::where('type', $type)->get();
    }

    public function checkIfHasDefaultAddress($customerId, $addressId, $type)
    {
        if ($type == 'billing') {
            $field = 'default_billing';
        } else {
            $field = 'default_shipping';
        }
        return self::where('user_id', $customerId)
                    ->where('id', '!=', $addressId)
                    ->update([$field => 0]);
    }

    //Address country by id
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    //Address province by id
    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'id');
    }

    //Address city by id
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id');
    }
}
