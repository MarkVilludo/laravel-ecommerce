<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVoucher extends Model
{
    use SoftDeletes;

    public function voucher()
    {
        return $this->belongsTo('App\Models\Vouchers');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
