<?php

namespace App\Http\Controllers;

use App\CostType;
use Illuminate\Http\Request;

class CostTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function serveCostTypes()
    {
         return CostType::all();

    }
}
