<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    protected $table = 'package_items';

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
    public function variant()
    {
        return $this->belongsTo('App\Models\ProductVariant', 'variant_id', 'id');
    }
    public function getItems($packageId)
    {
        return self::where('package_id', $packageId);
    }
}
