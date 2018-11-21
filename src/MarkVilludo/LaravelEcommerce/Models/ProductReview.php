<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';

    //product reviews user
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->select('id', 'first_name', 'last_name', 'image_path');
    }
    //product
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
    //check if exist
    public function checkIfExisting($userId, $reviewId)
    {
        return self::where('user_id', $userId)->where('id', $reviewId)->first();
    }
    //get product ratings average
    public function scopeGetRatingsAverage($query, $productId)
    {
        return $query->selectRaw('product_id,AVG(product_reviews.rate) AS average_ratings')
                    ->where('product_id', $productId)
                    ->groupBy('product_id')
                    ->first();
    }
}
