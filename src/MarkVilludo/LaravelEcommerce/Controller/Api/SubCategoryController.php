<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Category\SubCategoryStoreRequest;
use App\Http\Resources\SubCategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use Validator;
use Response;
use Config;

class SubCategoryController extends Controller
{

   
    //construct model variable

    public function __construct(SubCategory $subCategory)
    {
        $this->subCategory = $subCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($categoryId)
    {
        return SubCategoryResource::collection($this->subCategory->where('category_id', $categoryId)
                                                                 ->with('products')
                                                                 ->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryStoreRequest $request, $categoryId)
    {
        // return $request->all();
        $newCategory = new $this->subCategory;
        $newCategory->title = $request->title;
        $newCategory->category_id = $categoryId;
        $newCategory->description = $request->description;
        $newCategory->save();
        $data['message'] = config('app_messages.SuccessAddedSubCategory');
        $statusCode = 200;
        
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($categoryId, $subCategoryId)
    {
        // return $request->all();
        $subCategory = $this->subCategory->where('category_id', $categoryId)->where('id', $subCategoryId)
                                         ->with('childSubCategories.products')
                                         ->first();


        if ($subCategory) {
            $statusCode = 200;
            $data['message'] = config('app_messages.ShowSubCategoryDetails');
            $data['sub_categories'] = new SubCategoryResource($subCategory);
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
    public function update(Request $request, $categoryId, $id)
    {
         // return $request->all();
        $rules = [
            'title' => 'required|'.Rule::unique('sub_categories')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $updateSubCategory = $this->subCategory->find($id);
            $updateSubCategory->title = $request->title;
            $updateSubCategory->description = $request->description;
            $updateSubCategory->save();

            $data['message'] = config('app_messages.SuccessUpdateSubCategory');
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
    public function destroy($categoryId, $subCategoryId)
    {
        //
        // return $subCategoryId;
        $subCategory = $this->subCategory->where('category_id', $categoryId)
                                         ->where('id', $subCategoryId)
                                         ->withTrashed()
                                         ->first();

        if ($subCategory) {
            if (!$subCategory->deleted_at) {
                $subCategory->delete();
                    $data['message'] = config('app_messages.SuccessDeleteSubCategory');
                    $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.AlreadyDeletedSubCategory');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.SubCategoryNotFound');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }
}
