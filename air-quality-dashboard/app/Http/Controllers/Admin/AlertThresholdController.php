<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertThreshold;
use Illuminate\Http\Request;

class AlertThresholdController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,monitoring-admin']);
    }
    
    public function index()
    {
        $thresholds = AlertThreshold::orderBy('min_value')->get();
        return view('admin.thresholds.index', compact('thresholds'));
    }
    
    public function edit(AlertThreshold $threshold)
    {
        return view('admin.thresholds.edit', compact('threshold'));
    }
    
    public function update(Request $request, AlertThreshold $threshold)
    {
        $validated = $request->validate([
            'min_value' => 'required|numeric',
            'max_value' => 'required|numeric|gte:min_value',
            'color_code' => 'required|string|max:7',
            'health_implications' => 'nullable|string',
        ]);
        
        $threshold->update($validated);
        
        return redirect()->route('admin.thresholds.index')
            ->with('success', 'Alert threshold updated successfully');
    }
}