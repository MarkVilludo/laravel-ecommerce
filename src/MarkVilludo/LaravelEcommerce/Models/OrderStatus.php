<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    use SoftDeletes;

    protected $table = 'order_status';

    //user has many notes
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'status_id', 'id');
    }
}
