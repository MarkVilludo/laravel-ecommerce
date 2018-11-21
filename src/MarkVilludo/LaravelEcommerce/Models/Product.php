<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\ProductResource;
use App\Models\ProductRecentlySearch;
use App\Models\ChildSubCategory;

class Product extends Model
{

    
    use SoftDeletes;
   
    protected $table = 'products';

    /**
     * Returns an array with properties which must be indexed
     *
     * @return array
     */
    public function getSearchableBody()
    {
        $searchableProperties = [
            'name' => $this->name,
            'category' => $this->category->title
        ];
        
        return $searchableProperties;
    }

     /**
     * Return the type of the searchable subject
     *
     * @return string
     */
    public function getSearchableType()
    {
        return 'product';
    }
    /**
     * Return the id of the searchable subject
     *
     * @return string
     */
    public function getSearchableId()
    {
        return $this->id;
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ChildSubCategory', 'child_sub_category_id', 'id');
    }
    //product has many variants
    public function variants()
    {
        return $this->hasMany('App\Models\ProductVariant');
    }
    //product has one fbt
    public function fbt()
    {
        return $this->belongsTo('App\Models\FBT', 'fbt_id', 'id');
    }
    //product has many frequently bought together products
    public function fbtProducts()
    {
        return $this->hasMany('App\Models\FBTProduct', 'fbt_id', 'fbt_id')->inRandomOrder();
    }
    //product has many images
    public function images()
    {
        return $this->hasMany('App\Models\ProductImage');
    }
    //product has many images
    public function latestImage()
    {
        return $this->hasMany('App\Models\ProductImage')->latest()->limit(4);
    }
    //product has default image
    public function defaultImage()
    {
        return $this->hasOne('App\Models\ProductImage')->where('page_preview', 1);
    }
    //product has default image
    public function childCategory()
    {
        return $this->belongsTo('App\Models\ChildSubCategory', 'child_sub_category_id', 'id');
    }
    //product has faq's
    public function faqs()
    {
        return $this->hasMany('App\Models\ProductFaq');
    }
    public function infos()
    {
        return $this->hasMany('App\Models\ProductInfo');
    }
    //product has reviews
    public function reviews()
    {
        return $this->hasMany('App\Models\ProductReview')->orderBy('created_at', 'desc');
    }
    //for withandwherehas
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }
    public function scopeGetPriceRange($query, $price)
    {
        if ($price) {
            return $query->whereBetween('selling_price', $price);
        }
    }
    public function scopeGetByNewArrival($query, $isNewArrival)
    {
        if ($isNewArrival) {
            return $query->where('is_new_arrival', 1);
        }
    }
    public function scopeGetMostPicks($query, $isTopPicks)
    {
        if ($isTopPicks) {
            return $query->where('featured', 1);
        }
    }
    public function scopeGetByName($query, $name, $userId)
    {
        if ($name) {
            //Store recently search
            //user_id, field,data
            if ($userId) {
                //check if exist before save new search details
                $checkExist = ProductRecentlySearch::where('text', $name)->first();
                if ($checkExist) {
                    //existing updated by
                    $checkExist->updated_at = date('Y-m-d H:i:s');
                    $checkExist->update();
                } else {
                    $recentSearch = new ProductRecentlySearch;
                    $recentSearch->user_id = $userId;
                    $recentSearch->text = $name;
                    $recentSearch->save();
                }
            }
            //End saved recently search
            return $query->where('name', 'like', '%' . $name . '%');
        }
    }
    //get by product variant colors
    public function scopeGetByVariantColor($query, $color)
    {
        if ($color) {
            return  $query->withAndWhereHas('variants', function ($query) use ($color) {
                        $query->withAndWhereHas('colors', function ($query) use ($color) {
                            if ($color) {
                                $query->where('color', $color);
                            }
                        });
            });
        }
    }
    //get by product child categories
    public function scopeGetByChildCategory($query, $category)
    {
        if ($category) {
            return  $query->withAndWhereHas('childCategory', function ($query) use ($category) {
                if ($category) {
                    $query->where('id', $category);
                }
            });
        }
    }
    //order by selected column
    public function scopeGetSortBy($query, $orderBy, $sortBy)
    {
        if ($orderBy == 'price') {
            $orderBy = 'selling_price';
        }
        // goes here
        if ($orderBy && $sortBy) {
            if ($orderBy == 'is_new_arrival') {
                return $query->where('is_new_arrival', 1)
                             ->orderBy('created_at', $sortBy);
            } else {
                return $query->orderBy($orderBy, $sortBy);
            }
        } elseif ($orderBy && !$sortBy) {
            return $query->orderBy($orderBy, 'asc');
        } elseif (!$orderBy && !$sortBy) {
            return $query->orderBy('ratings', 'asc');
        } else {
        }
    }
    //Get product with minimal stocks
    public function scopeGetProductMinimalStocks($query, $numberOfItems)
    {
        $products = $query->withAndWhereHas('variants', function ($query) {
                            $query->where('inventory', '<=', 5);
        })
                        ->paginate($numberOfItems);

        return ProductResource::collection($products);
    }
    public function scopeGetByCategory($query, $id, $numberOfItems)
    {
        $products = $query->withAndWhereHas('childCategory', function ($query) use ($id) {
                            $query->where('id', $id);
        })
                        ->paginate($numberOfItems);

        return ProductResource::collection($products);
    }
    //get frequently bought together
    public static function getFBTProducts($fbtProducts, $productId)
    {
        $fbtProductsArray = [];
        if ($fbtProducts && $productId) {
            foreach ($fbtProducts as $key => $fbtProduct) {
                if ($fbtProduct->product_id != $productId) {
                    $fileName = $fbtProduct->product->defaultImage ? $fbtProduct->product->defaultImage->file_name :'';
                    $DefImg = $fbtProduct->product->defaultImage ? url('/storage/products/'.$fileName):'';
                    $MedImg = $fbtProduct->product->defaultImage ? url('/storage/products/medium/'.$fileName) :'';
                    $SmImg = $fbtProduct->product->defaultImage ? url('/storage/products/small/'.$fileName) :'';
                    $XsmImg = $fbtProduct->product->defaultImage ? url('/storage/products/xsmall/'.$fileName) :'';

                    $fbtProductsArray[] = ['id' => $fbtProduct->id,
                        'product_id' => $fbtProduct->product_id,
                        'product' => new ProductResource($fbtProduct->product),
                        'product_name' => $fbtProduct->product->name,
                        'short_name' => str_limit($fbtProduct->product->name, 18),
                        'regular_price' => $fbtProduct->product->regular_price,
                        'selling_price' => $fbtProduct->product->selling_price,
                        'default_image' => $DefImg,
                        'medium_path' => $MedImg,
                        'small_path' => $SmImg,
                        'xsmall_path' => $XsmImg
                    ];
                }
            }
        }
        return $fbtProductsArray;
    }
}
