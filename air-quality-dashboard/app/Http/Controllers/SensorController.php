<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'is_active' => 'nullable|boolean',  // Allow null or boolean values
            ]);

            // Ensure 'is_active' defaults to false if it's not provided (checkbox unchecked)
            $validated['is_active'] = $validated['is_active'] ?? false;

            // Create a new sensor with validated data
            $sensor = Sensor::create($validated);

            // Return success response with the created sensor data
            return response()->json([
                'success' => true,
                'sensor' => $sensor,
                'message' => 'Sensor added successfully!'
            ], 201);  // 201 Created response

        } catch (ValidationException $e) {
            // Handle validation errors and return response with errors
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed'
            ], 422);

        } catch (\Exception $e) {
            // Log the exception and return a 500 Internal Server Error response
            Log::error('Sensor creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);  // 500 Internal Server Error
        }
    }

    public function destroy(Sensor $sensor)
    {
        try {
            // Delete the sensor
            $sensor->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Sensor deleted successfully!'
            ], 200);  // 200 OK response

        } catch (\Exception $e) {
            // Log the error and return server error response
            Log::error('Sensor deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sensor.'
            ], 500);  // 500 Internal Server Error
        }
    }
}
