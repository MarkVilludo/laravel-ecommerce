<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Journal\JournalCategoryStoreRequest;
use App\Http\Resources\JournalCategoryResource;
use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use Illuminate\Http\Request;

class JournalCategoryController extends Controller
{
    //
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
        //view and get data from api
        $data['categories'] = $this->journalCategory->paginate(5);
        return view('admin.journal_category.index', $data);
    }
}
