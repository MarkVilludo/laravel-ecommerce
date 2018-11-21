<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FBTProduct extends Model
{
    protected $table = 'fbt_products';
    
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
}
