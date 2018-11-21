<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use SoftDeletes;
   
    protected $table = 'sub_categories';

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function childSubCategories()
    {
        return $this->hasMany('App\Models\ChildSubCategory');
    }
}
