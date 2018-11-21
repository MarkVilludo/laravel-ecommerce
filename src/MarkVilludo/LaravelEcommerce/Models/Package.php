<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    public function items()
    {
        return $this->hasMany('App\Models\PackageItem');
    }

    //get by name
    public function scopeGetByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
        }
    }
    
    //product has package name
    public function category()
    {
        return $this->belongsTo('App\Models\ChildSubCategory', 'child_sub_category_id', 'id');
    }
}
