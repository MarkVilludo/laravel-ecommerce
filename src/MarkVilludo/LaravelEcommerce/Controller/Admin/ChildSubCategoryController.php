<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Category\ChildSubCategoryStoreRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ChildSubCategory;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Helpers\Helper;

use Storage;
use File;
use Validator;
use Response;
use Config;
use Session;

class ChildSubCategoryController extends Controller
{

   
    //construct model variable
    public function __construct(ChildSubCategory $childSubCategory)
    {
        $this->childSubCategory = $childSubCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['categories'] = $this->childSubCategory->where('category_id', config('setting.FSCategory'))
                                                     ->where('sub_category_id', config('setting.FSSubCategory'))
                                                     ->with('products')->paginate(10);

        return view('admin.category.index', $data);
    }

    /**
     * Create new child sub category
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.sub_category.child_category.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
   
        // return $request->all();
        //generated file name
        //call resize and crop images function
        $newChildSubCategory = new $this->childSubCategory;

        if ($request->file('file')) {
            $file = $request->file('file');
            $origFilePath = '/storage/categories';
            $filename = md5($file->getClientOriginalName());
            $filetype = $file->getClientOriginalExtension();
            Helper::storeImages($file, $origFilePath);
            $newChildSubCategory->file_name = $filename.'.'.$filetype;
            $newChildSubCategory->path = $origFilePath;
        }
        //end

        $newChildSubCategory->title = $request->name;
        $newChildSubCategory->description = $request->description;
        $newChildSubCategory->category_id = config('setting.FSCategory');
        $newChildSubCategory->sub_category_id = config('setting.FSSubCategory');
       
        $newChildSubCategory->save();

        $data['message'] = config('app_messages.SuccessCreateSubCategory');

        $statusCode = 200;
       
        Session::flash('message', $data['message']);
        return redirect()->back();
    }


    /**
     * Edit form for child sub categories
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // return $id;
        $data['childSubCategory'] = $this->childSubCategory->find($id);

        return view('admin.category.sub_category.child_category.edit', $data);
    }
}
