<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\ProductNewArrival;
use App\Http\Resources\ProductMostPicks;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\RecentlyViewResource;
use App\Http\Resources\FBTProductResource;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductRecentlySearch;
use App\Models\ProductVariantColor;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\RecentlyView;
use App\Models\ProductImage;
use App\Models\ProductInfo;
use App\Models\ProductFaq;
use App\Models\FBTProduct;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\Product;
use App\Helpers\Helper;
use App\Models\FBT;
use Response;
use Config;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        Product $product,
        ProductVariant $productVariant,
        ProductImage $productImage,
        ProductFaq $productFaq,
        ProductInfo $productInfo,
        FBT $fbt,
        FBTProduct $fbtProduct,
        RecentlyView $recentlyView,
        ProductVariantColor $productVariantColor,
        ProductRecentlySearch $recentlySearch,
        CartItem $cartItem,
        Wishlist $wishList
    ) {
        $this->productVariant = $productVariant;
        $this->product = $product;
        $this->productImage = $productImage;
        $this->productFaq = $productFaq;
        $this->productInfo = $productInfo;
        $this->fbt = $fbt;
        $this->fbtProduct = $fbtProduct;
        $this->recentlyView = $recentlyView;
        $this->productVariantColor = $productVariantColor;
        $this->recentlySearch = $recentlySearch;
        $this->cartItem = $cartItem;
        $this->wishList = $wishList;
    }

    public function index()
    {
        $products = $this->product->with('variants.image')
                                    ->with('images')->with('faqs')
                                    ->with('reviews')
                                    ->paginate(10);
        if ($products) {
            $data =  ProductResource::collection($products);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Search / Filter products
     *
     * @return \Illuminate\Http\Response
     */
    public function searchProduct(Request $request)
    {
        //Filters
        //return $request->all();
        //get current user id when api calls
        $user = $request->user('api');
        $userId =  $user ? $user->id : null;

        $priceRange = [];
        if ($request->price) {
            $priceRange = explode('-', $request->price);
        }
        $color = '';
        if ($request->color) {
            $color = '#'.$request->color;
        }

        $products = $this->product->getPriceRange($priceRange)
                            ->getByName($request->name, $userId)
                            ->getSortBy($request->sortBy, $request->orderBy)
                            ->getByVariantColor($color)
                            ->getByChildCategory($request->category)
                            ->getByNewArrival($request->is_new_arrival)
                            ->getMostPicks($request->is_most_picks)
                            ->with('variants.image')
                            ->with('images')->with('faqs')
                            ->paginate(10);
                            
        if ($products) {
            $data =  ProductResource::collection($products);
            return $data;
        } else {
            $data['message'] = 'Not products found.';
            $statusCode = 404;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Most pick product list
     *
     * @return \Illuminate\Http\Response
     */

    public function mostPickProducts()
    {
        $products = $this->product::where('featured', 1)
                            ->orderBy('ratings', 'asc')
                            ->paginate(10);



        if ($products) {
            $data['message'] = config('app_messages.ShowTopPicksProducts');
            return $data = ProductMostPicks::collection($products);
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $data['data'] = null;
            $statusCode = 200;
        }
        
        return response()->json($data, $statusCode);
    }
     /**
     * New arrivals
     *
     * @return \Illuminate\Http\Response
     */
    public function newArrivals()
    {
        $products = $this->product->where('is_new_arrival', 1)
                                    ->orderBy('ratings', 'asc')
                                    ->paginate(10);

        if ($products) {
            $data['message'] = config('app_messages.ShowNewArrivalProducts');
            return $data = ProductNewArrival::collection($products);
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $data['data'] = null;

            $statusCode = 200;
        }
        return response()->json($data, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        // return $request->all();
        //Begin database transaction
        DB::beginTransaction();

        $failedCount = 0;

        $newProduct = new $this->product;
        $newProduct->name = $request->name;
        $newProduct->short_description = $request->short_description;
        $newProduct->description = $request->description;
        $newProduct->warranty_type = $request->warranty_type;
        $newProduct->warranty = $request->warranty;
        $newProduct->model = $request->model;
        $newProduct->minimum_purchase_quantity = $request->minimum_purchase_quantity;
        $newProduct->sub_category_id = config('setting.FSSubCategory');
        $newProduct->child_sub_category_id = $request->child_sub_category_id;
        $newProduct->status = $request->status;
        $newProduct->featured = $request->featured;
        $newProduct->is_new_arrival = $request->is_new_arrival;
        $newProduct->fbt_id = $request->fbt;
        $newProduct->regular_price = $request->regular_price;
        $newProduct->selling_price = $request->selling_price;
        $newProduct->manual = $request->manual;

        if ($newProduct->save()) {
            //check product image exist
            if ($request->product_image) {
                $newProductImage = new $this->productImage;
                $newProductImage->file_name = $request->uploaded_image_file_name;
                $newProductImage->product_id = $newProduct->id;
                $newProductImage->page_preview = 1;
                $newProductImage->path = '/storage/products';
                $newProductImage->save();
            }

            //Store product variants
            foreach ($request->variants as $key => $variant) {
                if ($variant['colors'] && $variant['stock'] != null) {
                    $newProductVariant = new $this->productVariant;
                    $newProductVariant->product_id = $newProduct->id;
                    $newProductVariant->inventory = $variant['stock'];

                    if ($newProductVariant->save()) {
                        //store productvariant colors
                        foreach ($variant['colors'] as $key => $color) {
                            $newProductColor = new $this->productVariantColor;
                            $newProductColor->variant_id = $newProductVariant->id;
                            $newProductColor->category_id = $request->child_sub_category_id;
                            $newProductColor->color = $color;
                            $newProductColor->save();
                        }
                    }
                } else {
                    //rollback anything if failed to save any of product variant
                    $failedCount += 1;
                }

                //check if with variant image
                if ($variant['image_path']) {
                    $newProductImage = new $this->productImage;
                    $newProductImage->file_name = $variant['file_name'];
                    $newProductImage->product_variant_id = $newProductVariant->id;
                    $newProductImage->product_id = $newProduct->id;
                    $newProductImage->path = '/storage/products';
                    $newProductImage->save();
                }
            }
            //Store product info
            foreach ($request->infos as $key => $info) {
                //check if the generated dynamic product faq is not null
                if ($info['title']) {
                    $newFaq = new $this->productInfo;
                    $newFaq->product_id = $newProduct->id;
                    $newFaq->title = $info['title'];
                    $newFaq->description = $info['description'] ? $info['description'] : '';
                    $newFaq->save();
                } else {
                    //rollback anything if failed to save any of product faqs
                    $failedCount += 1;
                }
            }

            if ($failedCount > 0) {
                DB::rollBack();
                $data['message'] = config('app_messages.FailedToCompleRequiredFields');
                $data['status'] = false;
                $statusCode = 200;
            } else {
                DB::commit();

                if (Cache::has('productDetails'.$newProduct->id)) {
                    //clear cache products
                    Cache::forget('productDetails'.$newProduct->id);
                    Cache::forget('categories');
                    Cache::forget('productCategories'.$newProduct->child_sub_category_id);
                    //end clear cache
                }

                $data['status'] = true;
                $data['message'] = config('app_messages.SucessCreateProduct');
                $statusCode = 200;
            }
        } else {
            $data['message'] = config('app_messages.FailedToSaveProduct');
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // return auth()->user();
        //Cache data
        if (Cache::has('productDetails'.$id)) {
            $productDetails = Cache::get('productDetails'.$id);
        } else {
            $productDetails = Cache::remember(
                'productDetails'.$id,
                config('cache.cacheTime'),
                function () use ($id) {
                    return  $this->product->where('id', $id)
                                      ->with('variants.image')->with('faqs')
                                      ->with('reviews')->with('images')
                                      ->first();
                }
            );
        }
        //end cache product details

        // return $fbtProductsArray;
        if ($productDetails) {
            $product = new ProductResource($productDetails);

            $user = $request->user('api');
            $userId =  $user ? $user->id : '';

            if ($userId) {
                //store recently viewed products in user if exist
                //check if not exist in user
                $checkIfExist = $this->recentlyView->where('user_id', $userId)
                                                   ->where('product_id', $id)
                                                   ->first();
                if (!$checkIfExist) {
                    //check if exist product before save into db

                    $recentlyView = new $this->recentlyView;
                    $recentlyView->user_id = $userId;
                    $recentlyView->product_id = $id;
                    $recentlyView->views = 1;
                    if ($recentlyView->save()) {
                        $data['view_message'] = config('app_messages.SuccessSaveViewProduct');
                    } else {
                        $data['view_message'] = config('app_messages.FailedToSaveRecentlyViewed');
                    }
                } else {
                    ////forget recently views refresh data
                    Cache::forget('recently_views');
                    //end clear cache
                    $checkIfExist->increment('views');

                    $data['view_message'] = config('app_messages.SuccessUpdateCountViews');
                }
                //Check if added in shopping bag
                if (count($productDetails->variants) > 0) {
                    $isAddedInBags = $this->cartItem->where('product_id', $id)
                                                            ->where('variant_id', $productDetails->variants[0]['id'])
                                                            ->where('user_id', $userId)
                                                            ->first();

                    $data['is_added_bags'] = $isAddedInBags ? true : false;

                    $isAddedInWishList = $this->wishList->where('product_id', $id)
                                                            ->where('variant_id', $productDetails->variants[0]['id'])
                                                            ->where('user_id', $userId)
                                                            ->first();
                    //Check if added in wishlist
                    $data['is_added_wishlist'] = $isAddedInWishList ? true : false;
                } else {
                    $data['is_added_bags'] = false;
                    $data['is_added_wishlist'] = false;
                }
            }


            $data['message'] = config('app_messages.ShowProductDetails');

            $statusCode = 200;

            $data['userId'] = $userId;
            $data['product'] = $product;
            
            //fbt products not include current viewed product
            $fbtProductsArray = Product::getFBTProducts($productDetails->fbtProducts, $id);
           
            $data['fbt_products_without_parent'] = $fbtProductsArray;
        } else {
            $data['message'] = config('app_messages.ProductNotFound');
            $statusCode = 404;
        }

        return response()->json($data, $statusCode);
    }
     /**
     * Get recently views
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function recentlyViews(Request $request, $userId)
    {
        $recently_views = $this->recentlyView->where('user_id', $userId)
                                              ->getExceptProduct($request->except_product_id)
                                              ->with('product')
                                              ->get();

        if ($recently_views) {
            $data =  RecentlyViewResource::collection($recently_views);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    //Search recently viewed products
    public function searchRecentlyViewed(Request $request, $userId)
    {
        $recently_views = $this->recentlyView::withAndWhereHas('product', function ($query) use ($request) {
                                            $query->where('name', 'like', '%' . $request->name . '%');
        })
                                        ->where('user_id', $userId)
                                        ->get();

        if ($recently_views) {
            $data =  RecentlyViewResource::collection($recently_views);
            return $data;
        } else {
            $data['message'] = config('app_messages.ProductNotFound');
            $statusCode = 404;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Get recently search
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recentlySearch(Request $request, $userId)
    {
        // return $userId;
        $recentlySearch = $this->recentlySearch->where('user_id', $userId)->with('product')->paginate(10);

        if ($recentlySearch) {
            $data =  RecentlySearchResource::collection($recentlySearch);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Check if exist in shopping bags and in wishlist
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkExistBagsAndWishlist(Request $request)
    {
        // return $request->all();
        $user = $request->user('api');
        $userId = $user->id;
       //Check if added in shopping bag
        $isAddedInBags = $this->cartItem->where('product_id', $request->product_id)
                                                ->where('variant_id', $request->variant_id)
                                                ->where('user_id', $userId)
                                                ->first();

        $data['is_added_bags'] = $isAddedInBags ? true : false;

        $isAddedInWishList = $this->wishList->where('product_id', $request->product_id)
                                                    ->where('variant_id', $request->variant_id)
                                                    ->where('user_id', $userId)
                                                    ->first();
        //Check if added in wishlist
        $data['is_added_wishlist'] = $isAddedInWishList ? true : false;

        $data['message'] = config('app_messages.ShowsProductIfInBagsAndWishlists');

        $statusCode = 200;

        return response()->json($data, $statusCode);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $id;
        $product = $this->product->where('id', $id)->first();

        if ($product) {
            //get all variants
            $variants = $this->productVariant->where('product_id', $product->id)->get();

            foreach ($variants as $key => $variant) {
                //delete variant
                $variant->delete();
               //delete also its variant / colors variant images
                $colors = $this->productVariantColor->where('variant_id', $variant->id)->delete();
            }

            //delete also product infos
            $this->productInfo->where('product_id', $product->id)->delete();

            //delete also all recently views under this product to avoid errors or "trying to get property non-object"
            $this->recentlyView->where('product_id', $product->id)->delete();

            //delete also this product in customer bags and wishlists
            $this->cartItem->where('product_id', $product->id)->delete();
            $this->wishList->where('product_id', $product->id)->delete();

            //delete all product variants
            $this->productVariant->where('product_id', $product->id)->delete();

            //delete also this product in frequently bought together
            $this->fbtProduct->where('product_id', $product->id)->delete();

            $product->delete();

            if (Cache::has('productDetails'.$product->id)) {
                //clear cache products
                    Cache::forget('productDetails'.$product->id);
                    Cache::forget('categories');
                    Cache::forget('productCategories'.$newProduct->child_sub_category_id);
                    //end clear cache
                //end clear cache
            }

            $data['message'] = config('app_messages.SuccessDeleteProduct');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.ProductNotFound');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }
    //upload image
    public function uploadImage(Request $request)
    {
        // return $request->all();
        //check product image exist
        if ($request->file('file')) {
            //call resize and crop images function
                $file = $request->file('file');
                $origFilePath = '/storage/products';
                $filename = md5($file->getClientOriginalName());
                $filetype = $file->getClientOriginalExtension();
                Helper::storeImages($file, $origFilePath);
            //end
            $url_path = $origFilePath.'/'.$filename.'.'.$filetype;
            $data['path'] = $url_path;

            $data['file_name'] = $filename.'.'.$filetype;
            $data['url_path'] = url($url_path);
            $data['variant_key'] = $request->variantKey;

            if ($request->store_variant) {
                $newProductImage = new $this->productImage;
                $newProductImage->file_name = $filename.'.'.$filetype;
                $newProductImage->product_id = $request->product_id;
                $newProductImage->product_variant_id = $request->variant_id;
                $newProductImage->path = '/storage/products';
                $newProductImage->save();
            }

            return $data;
        }
    }
}
