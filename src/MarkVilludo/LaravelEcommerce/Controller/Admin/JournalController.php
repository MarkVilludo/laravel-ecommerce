<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Journal\JournalStoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use App\Models\Journal;
use Response;
use Session;

class JournalController extends Controller
{

   
    public function __construct(Journal $journal, JournalCategory $journalCategory)
    {
        $this->journal = $journal;
        $this->journalCategory = $journalCategory;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data['journals'] = $this->journal->paginate(10);
        //Get data from api
        return view('admin.journal.index');
    }
    public function create()
    {
        // return 'teadewa';
        
        return view('admin.journal.create');
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
        $data['journalId'] = $id;

        return view('admin.journal.edit', $data);
    }
}
