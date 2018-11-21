<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $table = 'journal';

    public function category()
    {
        //journal belongs to category
        return $this->belongsTo('App\Models\JournalCategory', 'journal_category_id', 'id');
    }

    // journal has many i
    public function sliders()
    {
        //journal belongs to category
        return $this->hasMany('App\Models\JournalSlider');
    }
    public function scopeGetByName($query, $title)
    {
        if ($title) {
            return $query->where('title', 'like', '%' . $title . '%');
        }
    }
    public function scopeGetCategory($query, $category)
    {
        if ($category) {
            return $query->where('journal_category_id', $category);
        }
    }
}
