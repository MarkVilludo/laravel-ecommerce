<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherBind extends Model
{
    protected $table = 'voucher_bind';

    public function voucher()
    {
        return $this->belongsTo('App\Voucher');
    }

    public function itemBinded()
    {
    // return $this->belongsTo(Product, $this->key_name, 'fk_id');
    // return $this->belongsTo($this->model, $this->key_name, 'fk_id');
    }
}
