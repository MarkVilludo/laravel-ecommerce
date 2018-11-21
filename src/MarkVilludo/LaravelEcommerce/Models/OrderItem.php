<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductVariant;

class OrderItem extends Model
{
    use SoftDeletes;
   
    protected $table = 'order_items';
    // protected $primaryKey = 'order_item_id';

    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withTrashed();
    }
    
    public function productImage()
    {
        return $this->belongsTo('App\Models\ProductImage')->where('page_preview', 1);
    }

    //return order items quantity to variant inventory when cancel order
    public function returnOrderItemQuantity($orderItemId)
    {
        $orderItem = self::find($orderItemId);
        //get and return quantity to stocks each order item
        $variant = ProductVariant::find($orderItem->variant_id);
        if ($variant) {
            $variant->increment('inventory', $orderItem->quantity);
        }
    }

    //if has package items
    public function packageItems()
    {
        return $this->hasMany('App\Models\PackageItem', 'package_id', 'package_id');
    }

    //Order has one user
    public function status()
    {
        return $this->hasOne('App\Models\OrderStatus', 'id', 'status_id');
    }

    //Each item has many notes
    public function notes()
    {
        return $this->hasMany('App\Models\OrderNote', 'order_item_id', 'id')->orderBy('created_at', 'desc');
    }
}
