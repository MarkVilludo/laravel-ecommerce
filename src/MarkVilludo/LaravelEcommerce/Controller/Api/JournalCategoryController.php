<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Journal\JournalCategoryUpdateRequest;
use App\Http\Requests\Api\Journal\JournalCategoryStoreRequest;
use App\Http\Resources\JournalCategoryResource;
use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use Illuminate\Http\Request;

class JournalCategoryController extends Controller
{

   
    public function __construct(JournalCategory $journalCategory)
    {
        $this->journalCategory = $journalCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $journalCategory = $this->journalCategory->orderBy('created_at', 'desc')
                                          ->paginate(10);

        if ($journalCategory) {
            $data = JournalCategoryResource::collection($journalCategory);
            return $data;
        } else {
            $data['message'] = config('app_messages.TherIsNoJournalCategoriesAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JournalCategoryStoreRequest $request)
    {
        // return $request->all();
        $newCategory = new $this->journalCategory;
        $newCategory->name = $request->name;
        $newCategory->save();

        $statusCode = 200;
        $data['message'] = config('app_messages.SuccessAddedJournalCategory');
       
        return response()->json($data, $statusCode);
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JournalCategoryUpdateRequest $request, $id)
    {
        //
        // return $request->all();
        $journalCategory = $this->journalCategory->find($id);
        $journalCategory->name = $request->name;
        if ($journalCategory->update()) {
            $data['message'] = config('app_messages.SuccessUpdatedJournalCategory');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        
        return response()->json($data, $statusCode);
    }
    /**
     * Remove journal categories
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $id;
        $journalCategory = $this->journalCategory->find($id);
        if ($journalCategory) {
            if ($journalCategory->delete()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessDeletedJournalCategory');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundJournalCategory');
        }
        
        return response()->json($data, $statusCode);
    }
}
