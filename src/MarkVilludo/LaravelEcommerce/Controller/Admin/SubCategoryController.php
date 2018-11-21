<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Category\SubCategoryStoreRequest;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\SubCategory;
use App\Models\Category;
use Validator;
use Response;
use Config;
use Session;

class SubCategoryController extends Controller
{

   
    //construct model variable
    public function __construct(SubCategory $subCategory, Category $category)
    {
        $this->subCategory = $subCategory;
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($categoryId)
    {
        //
        // return $categoryId;
        $data['category'] = $this->category->find($categoryId);

        return view('admin.category.sub_category.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryStoreRequest $request)
    {
        // return $request->all();
        $newCategory = new $this->subCategory;
        $newCategory->title = $request->title;
        $newCategory->category_id = $request->category_id;
        $newCategory->description = $request->description;
        if ($newCategory->save()) {
            $message = config('app_messages.SuccessAddedSubCategory');
            $statusCode = 200;
        } else {
            $message = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }

        Session::flash('message', $message);
        return redirect()->back();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        // return 'test';
        $data['subCategory'] = new SubCategoryResource($this->subCategory->where('id', $id)
                                                                         ->with('childSubCategories')
                                                                         ->first());

        return view('admin.category.sub_category.edit', $data);
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
        //
        $rules = [
            'title' => 'required|'.Rule::unique('sub_categories')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = [$validator->errors()];
            $statusCode = 422;
        } else {
            $updateSubCategory = $this->subCategory->find($id);
            $updateSubCategory->title = $request->title;
            $updateSubCategory->description = $request->description;
            $updateSubCategory->save();

            $message = config('app_messages.SuccessUpdateSubCategory');
            $statusCode = 200;
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
    public function destroy($subCategoryId)
    {
        // return $subCategoryId;
        $subCategory = $this->subCategory->where('id', $subCategoryId)->withTrashed()->first();

        if ($subCategory) {
            if (!$subCategory->deleted_at) {
                $subCategory->delete();
                    $message = config('app_messages.SuccessDeleteSubCategory');
                    $statusCode = 200;
            } else {
                $message = config('app_messages.AlreadyDeletedSubCategory');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.SubCategoryNotFound');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
