<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';

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
    public function scopeGetByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
    }
}
