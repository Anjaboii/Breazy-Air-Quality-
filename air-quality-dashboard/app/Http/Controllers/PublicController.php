<?php

namespace App\Http\Controllers;

use App\Models\AlertThreshold;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $thresholds = AlertThreshold::orderBy('min_value')->get();
        return view('public.index', compact('thresholds'));
    }
}