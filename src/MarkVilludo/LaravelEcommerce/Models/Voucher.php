<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Carbon\Carbon;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
                            'code',
                            'name',
                            'description',
                            'uses',
                            'max_uses',
                            'max_uses_user',
                            'type',
                            'discount_amount',
                            'is_fixed',
                            'starts_at',
                            'expires_at',
                            'model',
                            'max_amt_cap',
                            'min_amt_availability',
                            'is_enabled'
                        ];

    protected $dates = ['starts_at', 'expires_at'];

    // voucher that is used by users
    public function users()
    {
        return $this->hasMany('App\Models\VoucherUsers');
    }

    // Voucher table relationship to voucher_mode table
    public function voucherModel()
    {
        return $this->belongsTo('App\Models\VoucherModels', 'model');
    }

    public function bind()
    {
        return $this->hasMany('App\Models\VoucherBind', 'voucher_id');

        $model_id = $this->model;
        switch ($model_id) {
            case '2':
                return $this->hasMany('App\Models\VoucherBind', 'fk_id');
                break;
            case '1':
            default:
                return $this->hasMany('App\Models\VoucherBind', 'fk_id');
                // break;
            
                // return null;
                break;
        }
    }
    // check if a certain voucher exist base on code and date validation
    public function scopeCheckExistence($query, $code, $starts_at, $expires_at)
    {
        $starts_at_mod = Carbon::parse($starts_at)->addSeconds(1)->format('Y-m-d H:i:s');
        $expires_at_mod = Carbon::parse($expires_at)->subSeconds(1)->format('Y-m-d H:i:s');
        return $query->where('code', $code)->enabled()
            ->where(function ($q) use ($starts_at, $expires_at, $starts_at_mod, $expires_at_mod) {
                $q->where(function ($w) use ($starts_at, $expires_at) {
                    $w->where('starts_at', '>=', $starts_at)
                    ->where('starts_at', '<', $expires_at)
                    ->where('expires_at', '>=', $expires_at);
                })
                ->orWhere(function ($w) use ($starts_at, $expires_at) {
                    $w->where('expires_at', '<=', $expires_at)
                    ->where('expires_at', '>', $starts_at)
                    ->where('starts_at', '<=', $starts_at);
                })

                ->orWhere(function ($w) use ($starts_at, $expires_at) {
                    $w->where('starts_at', '<=', $starts_at)
                    ->where('expires_at', '>=', $expires_at);
                })
                ->orWhereBetween('starts_at', [$starts_at_mod, $expires_at_mod])
                ->orWhereBetween('expires_at', [$starts_at_mod, $expires_at_mod]);
            });
    }

    public function scopeSearchColumns($query, $searchBy, $searchString)
    {
        return $query->where($searchBy, 'RLIKE', "[[:<:]]".$searchString);
    }

    public function scopeOrderColumns($query, $sortBy = 'created_at', $sortOrder = 'desc')
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    public function scopeValidateCode($query, $code)
    {
        $today = Carbon::now();
        $code = strtoupper($code);
        $valid = $query->where('code', $code)->where('is_enabled', true)->where('starts_at', '<=', $today)
                                             ->where('expires_at', '>=', $today);

        return $valid;
    }

    // vouchers that can be used
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    // vouchers that cannot be used
    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }

    // vouchers that that are active - enabled and not expired
    public function scopeActive($query)
    {
        $today = Carbon::now();
        return $query->where('is_enabled', true)->where('expires_at', '>', $today);
    }

    // vouchers that that are inactive - (enabled and expired) and/or
    // (disabled and expired) and/or (enabled and not expired)
    public function scopeInactive($query)
    {
        $today = Carbon::now();
        return $query->where(function ($q) use ($today) {
            // (enabled and expired)
            $q->where(function ($w) use ($today) {
                $w->where('is_enabled', true)
                ->where('expires_at', '>', $today);
            })
            // (disabled and expired)
            ->orWhere(function ($w) use ($today) {
                $w->where('expires_at', '<', $today)
                ->where('is_enabled', false);
            })
            // (enabled and not expired)
            ->orWhere(function ($w) use ($today) {
                $w->where('expires_at', '>=', $today)
                ->where('is_enabled', true);
            });
        });
    }

    public function getStatusAttribute()
    {
        return $this->expires_at > Carbon::now() ? ($this->is_enabled == true ? 'active' : 'inactive') : 'expired';
    }

    public function getDiscountAttribute()
    {
        return $this->is_fixed == 1 ? 'P '.number_format($this->discount_amount, 2) : $this->discount_amount.'%';
    }

    public function getStartDatetimeAttribute()
    {
        return $this->starts_at->format('d M Y H:i:s');
    }

    public function getExpiryDatetimeAttribute()
    {
        return $this->expires_at->format('d M Y H:i:s');
    }

    // blade
    public function getStartDateAttribute()
    {
        return $this->starts_at->format('Y-m-d');
    }
    public function getStartTimeAttribute()
    {
        return $this->starts_at->format('H:i');
    }
    public function getExpiryDateAttribute()
    {
        return $this->expires_at->format('Y-m-d');
    }
    public function getExpiryTimeAttribute()
    {
        return $this->expires_at->format('H:i');
    }
    public function getModelNameAttribute()
    {
        return $this->model ? $this->name : 'None';
    }
    public function scopeSearchByDateRange($query, $starts_at, $expires_at)
    {
        if ($starts_at && $expires_at) {
            $query->whereBetween('starts_at', [$starts_at, $expires_at]);
        } elseif ($starts_at && !$expires_at) {
            $expires_at = date('Y-m-d');
            $query->whereBetween('starts_at', [$starts_at, $expires_at]);
        } elseif (!$starts_at && $expires_at) {
            $starts_at = date('Y-m-d');
            $query->whereBetween('starts_at', [$starts_at, $expires_at]);
        }
    }
}
