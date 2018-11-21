<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Product\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\ProductImage;
use App\Models\Product;
use App\Helpers\Helper;
use App\Models\FBT;

use Validator;
use Response;
use Config;
use Storage;
use File;
use Session;

class ProductController extends Controller
{

   
    /**
     * construct model variable
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        Product $product,
        ProductVariant $productVariant,
        SubCategory $subCategory,
        ProductImage $productImage,
        FBT $fbt
    ) {
        $this->productVariant = $productVariant;
        $this->product = $product;
        $this->subCategory = $subCategory;
        $this->productImage = $productImage;
        $this->fbt = $fbt;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $products = ProductResource::collection($this->product->paginate(5));

        //get products with minimal stocks left (5 item total number item in all variants)

        if ($products) {
            $data['message'] = config('app_messages.ShowProductList');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
        }

        $data['products'] = $products;
        
        return view('admin.product.index', $data);
    }
    /**
     * Most pick product list
     *
     * @return \Illuminate\Http\Response
     */

    public function mostPickProducts()
    {
        return $this->product->where('featured', 1)->orderBy('views', 'asc')->get();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        //Default for the mean time sub categories makeup and health and beauty
        //Categories (4) Sub categories (5)
        $subCategories = $this->subCategory->where('id', config('setting.FSSubCategory'))
                                            ->with('childSubCategories')
                                            ->first();

        $data['categories'] = $subCategories->childSubCategories;
        
        return view('admin.product.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        //
        // return $request->all();
        $newProduct = new $this->product;
        $newProduct->name = $request->name;
        $newProduct->sub_category_id = config('setting.FSSubCategory');
        $newProduct->child_sub_category_id = $request->child_sub_category_id;
        $newProduct->status = $request->status;
        $newProduct->featured = $request->featured;
        $newProduct->fbt_id = $request->fbt_id;
        $newProduct->regular_price = $request->regular_price;
        $newProduct->selling_price = $request->selling_price;

        if ($newProduct->save()) {
            //check product image exist
            if ($request->file('file')) {
                //call resize and crop images function
                    $file = $request->file('file');
                    $origFilePath = '/storage/products';
                    $filename = md5($file->getClientOriginalName());
                    $filetype = $file->getClientOriginalExtension();
                    Helper::storeImages($file, $origFilePath);
                //end
                $newProductImage = new $this->productImage;
                $newProductImage->file_name = $filename.'.'.$filetype;
                $newProductImage->product_id = $newProduct->id;
                $newProductImage->page_preview = 1;
                $newProductImage->path = $origFilePath;
                $newProductImage->save();
            }

            //Store product variants
            foreach ($request->colors as $key => $color) {
                $newProductVariant = new $this->productVariant;
                $newProductVariant->product_id = $newProduct->id;
                $newProductVariant->color = $color;
                $newProductVariant->inventory = $request->stocks[$key];
                // $newProductVariant->sku = $variant['sku'];
                $newProductVariant->size = $request->sizes[$key];
                // $newProductVariant->price = $variant['price'];
                $newProductVariant->save();
            }

            $data['message'] = config('app_messages.SucessCreateProduct');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.FailedToSaveProduct');
            $statusCode = 400;
        }
        return redirect('/products');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       //get data from api
        $data['productId'] = $id;
        return view('admin.product.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        // update product details.
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'regular_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'child_sub_category_id' => 'required|integer',
            'fbt' => 'required|integer',
            'status' => 'required|integer'
         ];

         $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
               $productUpdate = $this->product->find($id);
               
               $nArvl = $request->is_new_arrival != null ?  $request->is_new_arrival : $productUpdate->is_new_arrival;

               $productUpdate->name = $request->name;
               $productUpdate->child_sub_category_id = $request->child_sub_category_id;
               $productUpdate->description = $request->description;
               $productUpdate->short_description = $request->short_description;
               $productUpdate->status = $request->status;
               $productUpdate->featured = $request->featured;
               $productUpdate->is_new_arrival = $nArvl;
               $productUpdate->regular_price = $request->regular_price;
               $productUpdate->selling_price = $request->selling_price;
               $productUpdate->fbt_id = $request->fbt;
               $productUpdate->manual = $request->manual;

            if ($productUpdate->update()) {
                if (Cache::has('productDetails'.$id)) {
                    //clear cache products
                        Cache::forget('productDetails'.$id);
                        Cache::forget('categories');
                        Cache::forget('productCategories'.$productUpdate->child_sub_category_id);
                        //end clear cache
                    //end clear cache
                }
                //check product image exist
                if ($request->file('file')) {
                    // for upload image
                    $file = $request->file('file');
                    $origFilePath = '/storage/products';
                    $filename = md5($file->getClientOriginalName());
                    $filetype = $file->getClientOriginalExtension();
                    //generated file name
                    $updateProductImage = $this->productImage->where('product_id', $id)->first();
                    if ($updateProductImage) {
                        // $path = $request->file('file')->storeAs('public/products', $filename);
                        //call resize and crop images function
                        Helper::storeImages($file, $origFilePath);
                        //end
                        //remove existing image in storage
                        Storage::delete('public/products/'.$updateProductImage->file_name);
                        Storage::delete('public/products/medium/'.$updateProductImage->file_name);
                        Storage::delete('public/products/small/'.$updateProductImage->file_name);
                        Storage::delete('public/products/xsmall/'.$updateProductImage->file_name);

                        $updateProductImage->file_name = $filename.'.'.$filetype;
                        $updateProductImage->product_id = $id;
                        $updateProductImage->page_preview = 1;
                        $updateProductImage->path = $origFilePath;
                        $updateProductImage->save();
                    } else {
                        //call resize and crop images function
                        Helper::storeImages($file, $origFilePath);
                        //end
                   
                        $newProductImage = new $this->productImage;
                        $newProductImage->file_name = $filename.'.'.$filetype;
                        $newProductImage->product_id = $id;
                        $newProductImage->page_preview = 1;
                        $newProductImage->path = $origFilePath;
                        $newProductImage->save();
                    }
                }

                 //Store product variants
                if ($request->colors) {
                    $variants = $this->productVariant->where('product_id', $id)->get();

                    foreach ($variants as $key => $variant) {
                        $updateProductVariant = $this->productVariant->find($variant->product_variant_id);
                        $updateProductVariant->product_id = $id;
                        $updateProductVariant->color = $request->colors[$key];
                        $updateProductVariant->inventory = $request->stocks[$key];
                        // $updateProductVariant->sku = $variant['sku'];
                        $updateProductVariant->size = $request->sizes[$key];
                        // $updateProductVariant->price = $variant['price'];
                        $updateProductVariant->save();
                    }
                }
                
                    $message = config('app_messages.SuccessUpdatedProduct');
                    $statusCode = 200;
            } else {
                $message = config('app_messages.FailedUpdateProduct');
                $statusCode = 400;
            }
        }
            Session::flash('message', $message);
            return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
