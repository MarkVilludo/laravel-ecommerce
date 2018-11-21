<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\OrderResource;

class Order extends Model
{
    use SoftDeletes;
    
    protected $table = 'orders';
    // protected $primaryKey = 'order_id';

    //user has many notes
    public function notes()
    {
        return $this->hasMany('App\Models\OrderNote')->orderBy('created_at', 'desc');
    }

    //user has many items
    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\PaymentTransaction');
    }

    //Order has one user
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    //Order has one user
    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus');
    }

    //Order shipping address
    public function shippingAddress()
    {
        return $this->belongsTo('App\Models\CustomerAddress', 'customer_shipping_address_id', 'id')->withTrashed();
    }

    //Order billing address
    public function billingAddress()
    {
        return $this->belongsTo('App\Models\CustomerAddress', 'customer_billing_address_id', 'id')->withTrashed();
    }

    public function orderDetails($customerId, $orderId)
    {
        $customerOrders = self::where('id', $orderId)->where('user_id', $customerId)
                                        ->with('user')->with('notes')
                                        ->with('status')
                                        ->with('defaultBillingAddress')
                                        ->with('defaultShippingAddress')
                                        ->first();

        return $customerOrders;
    }

    //for withandwherehas
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }

    public function scopegetByOrderNumber($query, $orderNumber)
    {
        if ($orderNumber) {
            return $query->where('number', $orderNumber);
        }
    }

    public function scopeGetByStatus($query, $statusId)
    {
        if ($statusId) {
            $isReplacement = false;
            if ($statusId == config('setting.ReplacementStatus')) {
                $statusId = config('setting.ProcessingOrderStatus');
                $isReplacement  = true;
            }

            return  $query->withAndWhereHas('orderItems', function ($query) use ($statusId, $isReplacement) {
                    
                if ($isReplacement) {
                    $query->where('is_replacement', 1);
                } else {
                    $query->where('status_id', $statusId);
                }
            });
        }
    }

    //return order items quantity to variant inventory when cancel order
    public function returnOrderItemQuantity($orderId)
    {
    }

    //get orders for today with dynamic number of data in paginations
    public function scopeGetOrders($query)
    {
        $dateToday = date("Y-m-d", strtotime(date('Y-m-d')));
        $orders = $query->whereDate('created_at', '=', $dateToday)
                                                ->with('notes')
                                                ->with('orderItems')
                                                ->paginate(10);

        return $data = OrderResource::collection($orders);
    }

    //get orders for today count orders
    public function scopeGetOrdersCount($query)
    {
        $dateToday = date("Y-m-d", strtotime(date('Y-m-d')));
        return $query->whereDate('created_at', '=', $dateToday)
                                                ->with('notes')
                                                ->with('orderItems')
                                                ->count();
    }

    //get orders by scope user id
    public function scopeOwnOrders($query, $userId)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
    }

    //get count pending orders
    public function scopeCountPendingOrders($query)
    {
        $dateToday = date("Y-m-d", strtotime(date('Y-m-d')));

        return $query->whereDate('created_at', '=', $dateToday)
                     ->where('status_id', 1)
                     ->count();
    }
}
