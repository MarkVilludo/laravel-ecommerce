<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\PromoResource;

class Promo extends Model
{
    //
    protected $table = 'promos';

    public function scopeGetByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
    }

    public function scopeGetPromos($query, $numberOfItem)
    {
        $promos = $query->paginate($numberOfItem);

        return $data = PromoResource::collection($promos);
    }
}
