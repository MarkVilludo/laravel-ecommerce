<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FBT extends Model
{
    protected $table = 'frequently_bought_together';

    //FBT products
    public function fbtProducts()
    {
        return $this->hasMany('App\Models\FBTProduct', 'fbt_id', 'id')->orderBy('created_at', 'desc');
    }

    public function scopeGetByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
    }
}
