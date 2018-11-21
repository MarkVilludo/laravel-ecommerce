<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PriceRange;

class PriceRangeController extends Controller
{
    //
    public function __construct(PriceRange $priceRange)
    {
        $this->priceRange = $priceRange;
    }

    public function index()
    {
        return view('admin.price_range.index');
    }
}
