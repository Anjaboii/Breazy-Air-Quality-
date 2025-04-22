<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function locations()
{
    $sensors = Sensor::all();
    return view('admin.locations', compact('sensors'));
}
    public function dashboard()
    {
        $sensors = Sensor::count();
        $activeSensors = Sensor::where('is_active', true)->count();
        
        return view('admin.dashboard', compact('sensors', 'activeSensors'));
    }
    public function storeSensor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
    
        $sensor = Sensor::create($validated);
    
        return response()->json($sensor);
    }
    
    public function updateSensor(Request $request, Sensor $sensor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'is_active' => 'sometimes|boolean',
        ]);
    
        $sensor->update($validated);
    
        return response()->json($sensor);
    }
    
    public function deleteSensor(Sensor $sensor)
    {
        $sensor->delete();
        return response()->json(['message' => 'Sensor deleted successfully']);
    }
    

    
}