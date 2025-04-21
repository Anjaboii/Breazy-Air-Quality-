<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AqiLocation;
use App\Models\AqiSensor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AqiController extends Controller
{
    /**
     * Get all AQI locations with formatted response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocations()
    {
        try {
            $locations = AqiLocation::query()
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($location) {
                    return [
                        'id' => $location->id,
                        'location' => $location->name,
                        'latitude' => (float) $location->latitude,
                        'longitude' => (float) $location->longitude,
                        'aqi' => (int) $location->current_aqi,
                        'timestamp' => $location->updated_at->toIso8601String(),
                        'status' => $this->getAqiStatus($location->current_aqi)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $locations,
                'count' => $locations->count(),
                'last_updated' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch locations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all AQI sensors with formatted response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSensors()
    {
        try {
            $sensors = AqiSensor::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get()
                ->map(function ($sensor) {
                    return [
                        'id' => $sensor->id,
                        'name' => $sensor->name,
                        'description' => $sensor->description,
                        'latitude' => (float) $sensor->latitude,
                        'longitude' => (float) $sensor->longitude,
                        'status' => $sensor->status,
                        'last_active' => $sensor->last_active_at?->toIso8601String()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sensors,
                'count' => $sensors->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch sensors: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sensor data'
            ], 500);
        }
    }

    /**
     * Add new AQI location
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'current_aqi' => 'required|integer|min:0|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $location = AqiLocation::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Location added successfully',
                'data' => $location
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add location: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add location'
            ], 500);
        }
    }

    /**
     * Get AQI status based on AQI value
     *
     * @param int $aqi
     * @return string
     */
    protected function getAqiStatus(int $aqi): string
    {
        return match (true) {
            $aqi <= 50 => 'Good',
            $aqi <= 100 => 'Moderate',
            $aqi <= 150 => 'Unhealthy for Sensitive Groups',
            $aqi <= 200 => 'Unhealthy',
            $aqi <= 300 => 'Very Unhealthy',
            default => 'Hazardous'
        };
    }
}