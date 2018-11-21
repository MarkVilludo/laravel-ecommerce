<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    //
    protected $table = 'customer_wish_lists';

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
    //belongs to customer
    public function customer()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    //belongs to variant
    public function variant()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'variant_id', 'id');
    }
}
