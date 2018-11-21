<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVoucher extends Model
{
    use SoftDeletes;

    public function voucher()
    {
        return $this->belongsTo('App\Models\Vouchers');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
