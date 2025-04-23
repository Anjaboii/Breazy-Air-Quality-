<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AqiLocation;

class AdminController extends Controller
{
    // Toggle the sensor's active status
    public function toggleSensor(Sensor $sensor)
    {
        try {
            // Toggle the sensor's active status
            $sensor->is_active = !$sensor->is_active;
            $sensor->save();  // Save the updated status

            return response()->json([
                'success' => true,
                'sensor' => $sensor,
                'message' => 'Sensor status updated successfully!'
            ], 200);  // Return success with updated sensor data
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating sensor status: ' . $e->getMessage()
            ], 500);  // Return error if anything goes wrong
        }
    }

    // Display locations including AQI locations
    public function locations()
{
    $aqiLocations = AqiLocation::all();
    $locationCount = AqiLocation::count();
    $goodAqiCount = AqiLocation::where('aqi', '<=', 50)->count();
    
    return view('admin.locations', compact('aqiLocations', 'locationCount', 'goodAqiCount'));
}

public function storeLocation(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'aqi' => 'required|numeric'
    ]);

    $location = AqiLocation::create($validated);

    return response()->json([
        'success' => true,
        'location' => $location
    ]);
}


    // Dashboard method for displaying sensor stats
    public function dashboard()
    {
        $sensors = Sensor::all();
        return view('admin.dashboard', [
            'sensors' => $sensors,
            'sensorCount' => $sensors->count(),
            'activeSensorCount' => $sensors->where('is_active', true)->count()
        ]);
    }

    // Store a new sensor
    public function storeSensor(Request $request)
    {
        // Debug: Log incoming request
        Log::debug('Sensor Store Request:', $request->all());

        // Handle test request
        if ($request->has('test')) {
            return $this->handleTestRequest();
        }

        // Validate and create sensor
        try {
            $validatedData = $this->validateSensorData($request);
            $sensor = Sensor::create($validatedData);
            
            Log::info('Sensor created successfully', ['id' => $sensor->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sensor created successfully!',
                'sensor' => $sensor,
                'redirect' => route('admin.dashboard')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Sensor creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'received_data' => $request->all()
            ], 500);
        }
    }

    // Handle test request (for creating a test sensor)
    protected function handleTestRequest()
    {
        try {
            $testSensor = Sensor::create([
                'name' => 'TEST_SENSOR_'.rand(1000,9999),
                'latitude' => 6.9271,
                'longitude' => 79.8612,
                'is_active' => true
            ]);
            
            Log::info('Test sensor created', ['id' => $testSensor->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test sensor created! ID: '.$testSensor->id,
                'sensor' => $testSensor
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test sensor failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Test failed: '.$e->getMessage(),
                'solution' => 'Check database connection and migration'
            ], 500);
        }
    }

    // Validate sensor data before creating
    protected function validateSensorData(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
                'regex:/^-?\d{1,3}\.\d{1,8}$/'
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
                'regex:/^-?\d{1,3}\.\d{1,8}$/'
            ],
            'is_active' => 'boolean'
        ]);
    }
}
