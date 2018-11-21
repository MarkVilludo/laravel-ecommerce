<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Category\ChildSubCategoryStoreRequest;
use App\Http\Resources\ChildSubCategoryResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ChildSubCategory;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use Response;
use Config;

class ChildSubCategoryController extends Controller
{

   
    //construct model variable

    public function __construct(ChildSubCategory $childSubCategory, Product $product)
    {
        $this->childSubCategory = $childSubCategory;
        $this->product = $product;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Cache data
        if (Cache::has('categories')) {
            $categories = Cache::get('categories');
        } else {
            $categories = Cache::remember(
                'categories',
                config('cache.cacheTime'),
                function () {
                    return  $this->childSubCategory->where('category_id', config('setting.FSCategory'))
                                            ->where('sub_category_id', config('setting.FSSubCategory'))
                                            ->with('featuredProduct')
                                            ->paginate(10);
                }
            );
        }
        //end cache categories

        if ($categories) {
            return ChildSubCategoryResource::collection($categories);
        } else {
            $data['message'] = config('app_messages.CategoryNotFound');
            $statusCode = 404;
            return response()->json($data, $statusCode);
        }
    }
     /**
     * Search categories
     *
     * @return \Illuminate\Http\Response
    */
    public function searchCategories(Request $request)
    {
        // return $request->title;
        $categories = $this->childSubCategory->getByTitle($request->title)
                              ->paginate(5);

        if ($categories) {
            $data = ChildSubCategoryResource::collection($categories);
            return $data;
        } else {
            $data['message'] = config('app_messages.CategoryNotFound');
            $statusCode = 404;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChildSubCategoryStoreRequest $request)
    {
        //clear cache customers
        Cache::forget('categories');
        //end clear cache

        // return $request->all();
        $newChildSubCategory = new $this->childSubCategory;
        $newChildSubCategory->title = $request->title;
        $newChildSubCategory->description = $request->description;
        $newChildSubCategory->file_name = $request->image;
        $newChildSubCategory->path = $request->path;
        $newChildSubCategory->category_id = config('setting.FSCategory');
        $newChildSubCategory->sub_category_id = config('setting.FSSubCategory');
        $newChildSubCategory->save();

        if (Cache::has('categories')) {
            Cache::forget('categories');
        }

        $data['message'] = config('app_messages.SuccessCreateSubCategory');

        $statusCode = 200;
       
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Cache data
        if (Cache::has('productCategories'.$id)) {
            $productCategories = Cache::get('productCategories'.$id);
        } else {
            $productCategories = Cache::remember(
                'productCategories'.$id,
                config('cache.cacheTime'),
                function () use ($id) {
                    return  $this->product->with('variants.image')
                                ->with('images')->with('faqs')
                                ->with('fbtProducts')->with('reviews')
                                ->withAndWhereHas('childCategory', function ($query) use ($id) {
                                    $query->where('id', $id);
                                })
                                ->paginate(10);
                }
            );
        }
        //end cache products under selected category
      
        if ($productCategories) {
            $data =  ProductResource::collection($productCategories);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
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
        // return $id;
        $rules = [
            'title' => 'required|'.Rule::unique('child_sub_categories')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $updateChildSubCategory = $this->childSubCategory->find($id);
            $updateChildSubCategory->title = $request->title;
            $updateChildSubCategory->description = $request->description;
            
            if ($request->image) {
                $updateChildSubCategory->file_name = $request->image;
                $updateChildSubCategory->path = $request->path;
            }
           
            $updateChildSubCategory->category_id = config('setting.FSCategory');
            $updateChildSubCategory->sub_category_id = config('setting.FSSubCategory');
            
            $updateChildSubCategory->update();

            if (Cache::has('categories')) {
                Cache::forget('categories');
                Cache::forget('productCategories'.$id);
            }

            $data['message'] = config('app_messages.SuccessUpdateCategory');

            $statusCode = 200;
        }
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
        $category = $this->childSubCategory->find($id);
        if ($category) {
            if ($category->delete()) {
                if (Cache::has('categories')) {
                    Cache::forget('categories');
                    Cache::forget('productCategories'.$id);
                }
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessDeleteCategory');
            } else {
                $statusCode = 400;
                 $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
             $data['message'] = config('app_messages.CategoryNotFound');
        }
        return response()->json($data, $statusCode);
    }
}
