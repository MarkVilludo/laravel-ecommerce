<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChildSubCategory extends Model
{
    use SoftDeletes;
    
    protected $table = 'child_sub_categories';

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'child_sub_category_id', 'id');
    }
    public function scopeGetByTitle($query, $title)
    {
        if ($title) {
            return $query->where('title', 'like', '%' . $title . '%');
        }
    }
    public function featuredProduct()
    {
        return $this->hasOne('App\Models\Product', 'child_sub_category_id', 'id')->where('featured', 1)
                                    ->orderBy('updated_at', 'desc');
    }
}
