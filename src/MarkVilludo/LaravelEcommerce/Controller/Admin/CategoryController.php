<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Category\CategoryStoreRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Validator;
use Response;
use Config;
use Session;

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
        $data['categories'] = $this->category->paginate(10);

        return view('admin.category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        //
        $newChildSubCategory = new $this->category;
        $newChildSubCategory->title = $request->title;
        $newChildSubCategory->description = $request->description;
        $newChildSubCategory->save();

        $data['message'] = config('app_messages.SuccessCreateSubCategory');

        $statusCode = 200;
       
        return redirect()->route('categories.index', $data);
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
          // return $id;
        $data['category'] = new CategoryResource($this->category->where('id', $id)->with('subCategories')->first());

        return view('admin.category.edit', $data);
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
            'title' => 'required|'.Rule::unique('categories')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['message'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $updateChildSubCategory = $this->category->find($id);
            $updateChildSubCategory->title = $request->title;
            $updateChildSubCategory->description = $request->description;
            
            $updateChildSubCategory->save();

            $data['message'] = config('app_messages.SuccessCreateSubCategory');
        }

        return redirect()->route('categories.index', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($categoryId)
    {
        // return $statusId;
        $orderStatus = $this->category->where('id', $categoryId)->withTrashed()->first();

        if ($orderStatus) {
            if (!$orderStatus->deleted_at) {
                $orderStatus->delete();
                    $message = config('app_messages.SuccessDeleteCategory');
                    $statusCode = 200;
            } else {
                $message = config('app_messages.AlreadyDeletedCategory');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.CategoryNotFound');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
