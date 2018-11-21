<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Journal\JournalUpdateRequest;
use App\Http\Requests\Api\Journal\JournalStoreRequest;
use App\Http\Resources\JournalCategoryResource;
use App\Http\Resources\JournalResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use App\Models\JournalSlider;
use App\Models\Journal;

use Response;
use Storage;

class JournalController extends Controller
{
    //
    public function __construct(Journal $journal, JournalCategory $journalCategory, JournalSlider $journalSlider)
    {
        $this->journal = $journal;
        $this->journalCategory = $journalCategory;
        $this->journalSlider = $journalSlider;
    }
    /**
     * Display a listing of the journal.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get latest journals
        $journals = $this->journal->orderBy('created_at', 'desc')->paginate(10);

        if ($journals) {
            $data = JournalResource::collection($journals);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoJournalAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
     /**
     * Search journal
     *
     * @return \Illuminate\Http\Response
    */
    public function searchJournal(Request $request)
    {
        // return $request->title;
        $journals = $this->journal->getByName($request->title)
                                ->getCategory($request->category)
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        if ($journals) {
            $data = JournalResource::collection($journals);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoJournalAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Get 2 ramdom latest journals
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestJournal()
    {
        if (Cache::has('latestJournals')) {
            $journals = Cache::get('latestJournals');
        } else {
            $journals = Cache::remember('latestJournals', config('cache.cacheTime'), function () {
                return $this->journal::latest()
                                   ->orderBy('created_at', 'desc')
                                   ->limit(3)
                                   ->get();
            });
        }

        if ($journals) {
            $data = JournalResource::collection($journals);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoJournalAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    //Journal details
    public function show($id)
    {
        //check if exist
        if (Cache::has('journalDetails'.$id)) {
            $journalDetails = Cache::get('journalDetails'.$id);
        } else {
            $journalDetails = Cache::remember(
                'journalDetails'.$id,
                config('cache.cacheTime'),
                function () use ($id) {
                    return $this->journal->where('id', $id)
                                          ->with('category')
                                          ->with('sliders')
                                          ->first();
                }
            );
        }

        if ($journalDetails) {
            $data['message'] = config('app_messages.ShowsJournalDetails');
            $data['journal'] = new JournalResource($journalDetails);
            return $data;
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundJournal');

            return Response::json($data, $statusCode);
        }
    }

    //Journal details webview
    public function showWebView($id)
    {
        //check if exist
        $journal = $this->journal->where('id', $id)->with('category')->with('sliders')->first();

        if ($journal) {
            $data['journal'] = new JournalResource($journal);
            return view('admin.journal.list_webview', $data);
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.SucessDeletedJournal');

            return Response::json($data, $statusCode);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JournalStoreRequest $request)
    {
        //
        // return $request->all();
        $journal = new $this->journal;
        $journal->journal_category_id = $request->journal_category_id;
        $journal->content = $request->content;
        $journal->title = $request->title;
        $journal->file_name = $request->image;
        $journal->path = $request->file_name;
        if ($journal->save()) {
            if (Cache::has('latestJournals')) {
                //clear cache
                Cache::forget('latestJournals');
                //end clear cache
            }
            if ($request->sliders) {
                foreach ($request->sliders as $key => $slider) {
                    $journalSlider = new $this->journalSlider;
                    $journalSlider->journal_id = $journal->id;
                    $journalSlider->file_name = $slider['file_name'];
                    $journalSlider->path = $slider['path'];
                    $journalSlider->save();
                }
            }

            $data['message'] = config('app_messages.SuccessCreatedJournal');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }

        return Response::json($data, $statusCode);
    }


    //Journal sort by categories
    public function journalCategory($journalCategoryId)
    {
        // return $id;
        if (Cache::has('journalCategory'.$journalCategoryId)) {
            $journals = Cache::get('journalCategory'.$journalCategoryId);
        } else {
            $journals = Cache::remember(
                'journalCategory'.$journalCategoryId,
                config('cache.cacheTime'),
                function () use ($journalCategoryId) {
                    return $this->journal->where('journal_category_id', $journalCategoryId)->paginate(10);
                }
            );
        }

        if ($journals) {
            $data['message'] = config('app_messages.JournalsPerCategory');
            $data = JournalResource::collection($journals);
            return $data;
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.SucessDeletedJournal');

            return Response::json($data, $statusCode);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JournalUpdateRequest $request, $id)
    {
        // return $request->all();
        $journal = $this->journal->find($id);
        $journal->journal_category_id = $request->journal_category_id;
        $journal->content = $request->content;
        $journal->title = $request->title;

        if ($request->image) {
            $journal->file_name = $request->image;
            $journal->path = $request->file_name;
        }
        
        if ($journal->update()) {
            if (Cache::has('journalDetails'.$id)) {
                //clear cache products
                Cache::forget('journalDetails'.$id);
                Cache::forget('journalCategory'.$id);
                Cache::forget('latestJournals');
                //end clear cache
            }
            $data['message'] = config('app_messages.SuccessUpdatedJournal');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        
        return Response::json($data, $statusCode);
    }
    //delete image from journal sliders
    public function removeImageSlider($sliderId)
    {
        // return $customerId;
        $journalSlider = $this->journalSlider->find($sliderId);

        if ($journalSlider) {
            //if image exist then success force delete from the database.
            $journalSlider->delete();
            $statusCode = 200;

            //remove existing image in storage
            Storage::delete('public/journals/'.$journalSlider->file_name);
            Storage::delete('public/journals/xsmall/'.$journalSlider->file_name);
            Storage::delete('public/journals/small/'.$journalSlider->file_name);
            Storage::delete('public/journals/medium/'.$journalSlider->file_name);

            $data['message'] = config('app_messages.SuccessDeletedSliderImage');
        } else {
            $data['message'] = config('app_messages.NotFoundJournalSliderImage');
            $statusCode = 404;
        }

        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Remove journal
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteJournal($journalId)
    {
        // return $journalId;
        $journal = $this->journal->find($journalId);
        if ($journal) {
            if ($journal->delete()) {
                $statusCode = 200;

                if (Cache::has('journalDetails'.$journalId)) {
                    //clear cache products
                    Cache::forget('journalDetails'.$journalId);
                    //end clear cache
                }
                $data['message'] = config('app_messages.SucessDeletedJournal');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundJournal');
        }
        
        return Response::json($data, $statusCode);
    }
}
