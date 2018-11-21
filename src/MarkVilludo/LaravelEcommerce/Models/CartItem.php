<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
    public function package()
    {
        return $this->hasOne('App\Models\Package', 'id', 'package_id');
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function variant()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'variant_id', 'id');
    }
    //for withandwherehas
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }
}
