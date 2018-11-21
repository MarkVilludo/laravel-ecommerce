<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalCategory extends Model
{
    protected $table = 'journal_category';

    //Category has many journals
    public function journals()
    {
        return $this->hasMany('App\Models\Journal')->orderBy('created_at', 'desc');
    }
}
