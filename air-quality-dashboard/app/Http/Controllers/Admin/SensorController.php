<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,monitoring-admin']);
    }
    
    public function index()
    {
        $sensors = Sensor::with('latestReading')->get();
        return view('admin.sensors.index', compact('sensors'));
    }
    
    public function create()
    {
        return view('admin.sensors.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:5.5,10.0', // Approximate bounds for Sri Lanka
            'longitude' => 'required|numeric|between:79.0,82.0', // Approximate bounds for Sri Lanka
            'location_description' => 'required|string|max:255',
        ]);
        
        // Generate a unique sensor ID
        $sensorId = 'COL-' . strtoupper(Str::random(6));
        
        Sensor::create([
            'sensor_id' => $sensorId,
            'name' => $validated['name'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_description' => $validated['location_description'],
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor created successfully');
    }
    
    public function edit(Sensor $sensor)
    {
        return view('admin.sensors.edit', compact('sensor'));
    }
    
    public function update(Request $request, Sensor $sensor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:5.5,10.0',
            'longitude' => 'required|numeric|between:79.0,82.0',
            'location_description' => 'required|string|max:255',
        ]);
        
        $sensor->update($validated);
        
        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor updated successfully');
    }
    
    public function toggle(Sensor $sensor)
    {
        $sensor->is_active = !$sensor->is_active;
        $sensor->save();
        
        $status = $sensor->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.sensors.index')
            ->with('success', "Sensor {$status} successfully");
    }
}