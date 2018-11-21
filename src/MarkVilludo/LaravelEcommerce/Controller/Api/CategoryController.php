<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Category\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Category;
use Validator;
use Response;
use Config;

class CategoryController extends Controller
{

    //construct model variable

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Cache::has('categories')) {
            $categories = Cache::get('categories');
        } else {
            $categories = Cache::remember('categories', config('cache.cacheTime'), function () {
                return $this->category->with('subcategories.products')
                                      ->with('subcategories.childSubCategories.products')
                                      ->paginate(10);
            });
        }

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        // return $request->all();
        $newCategory = new $this->category;
        $newCategory->title = $request->title;
        $newCategory->description = $request->description;
        $newCategory->save();
        $data['message'] = config('app_messages.SuccessCreateCategory');

        $statusCode = 200;
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $id;
        $category = $this->category->where('id', $id)->with('subcategories.products')
                                                     ->with('subcategories.childSubCategories.products')
                                                     ->first();

        if ($category) {
            $statusCode = 200;
            $data['message'] = config('app_messages.ShowSubCategoryDetails');
            $data['category'] = new CategoryResource($category);
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.SubCategoryNotFound');
        }
        return Response::json(['data' => $data], $statusCode);
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
        // return $request->all();network
        $rules = [
            'title' => 'required|'.Rule::unique('categories')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['message'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $updateCategory = $this->category->find($id);
            $updateCategory->title = $request->title;
            $updateCategory->description = $request->description;
            $updateCategory->save();

            $data['message'] = config('app_messages.SuccessUpdateCategory');
            $statusCode = 200;
        }
        return Response::json(['data' => $data], $statusCode);
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
        $category = $this->category->where('id', $id)->first();

        if ($category) {
            $category->delete();
            $data['message'] = config('app_messages.SuccessDeleteCategory');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.CategoryNotFound');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }
}
