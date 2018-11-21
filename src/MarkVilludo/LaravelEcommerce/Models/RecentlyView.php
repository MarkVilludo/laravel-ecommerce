<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyView extends Model
{
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    //for withandwherehas
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }
    
    //scope where recently viewed except currently view product
    public function scopeGetExceptProduct($query, $productId)
    {
        if ($productId) {
            return $query->whereNOTIn('product_id', [$productId]);
        }
    }
}
