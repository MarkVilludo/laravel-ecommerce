<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'product_variants';

    //Variant has one image
    public function image()
    {
        return $this->hasOne('App\Models\ProductImage', 'product_variant_id', 'id');
    }
    //has many images
    public function images()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_variant_id', 'id');
    }
    //Variant has many colors
    public function colors()
    {
        return $this->hasMany('App\Models\ProductVariantColor', 'variant_id', 'id');
    }

    //for withandwherehas
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }
}
