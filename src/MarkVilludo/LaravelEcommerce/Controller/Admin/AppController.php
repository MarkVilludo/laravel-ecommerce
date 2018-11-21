<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    /**
     * About index
     *
     * @return \Illuminate\Http\Response
     */
    public function aboutIndex()
    {
        //
        return view('admin.pages.about.index');
    }

    /**
     * Terms condition index
     *
     * @return \Illuminate\Http\Response
     */
    public function termsConditionIndex()
    {
        //
        return view('admin.pages.terms_condition.index');
    }

    /**
     * Terms policy privacy index
     *
     * @return \Illuminate\Http\Response
     */
    public function privacyPolicyIndex()
    {
        //
        return view('admin.pages.privacy_policy.index');
    }

    /**
     * Return policy index
     *
     * @return \Illuminate\Http\Response
     */
    public function returnPolicyIndex()
    {
        //
        return view('admin.pages.return_policy.index');
    }

    /**
     * Return policy index
     *
     * @return \Illuminate\Http\Response
     */
    public function contactPageIndex()
    {
        //
        return view('admin.pages.contact.index');
    }
}
