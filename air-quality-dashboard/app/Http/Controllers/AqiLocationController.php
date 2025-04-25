<?php
namespace App\Http\Controllers;
/**
 * Get all AQI locations for the public dashboard
 * 
 * @return \Illuminate\Http\JsonResponse
 */
use App\Models\AqiLocation;

 class AqiLocationController extends Controller
 
 {
public function getPublicLocations()
{
    try {
        // Get only active locations with required fields
        $aqiLocations = AqiLocation::where('is_active', true)
            ->select(['id', 'name', 'latitude', 'longitude', 'aqi', 'updated_at'])
            ->orderBy('name')
            ->get();
            
        // Validate data before returning
        $validLocations = $aqiLocations->map(function ($location) {
            return [
                'id' => $location->id ?? null,
                'name' => $location->name ?? 'Unknown Location',
                'latitude' => $this->validateCoordinate($location->latitude),
                'longitude' => $this->validateCoordinate($location->longitude),
                'aqi' => $this->validateAqi($location->aqi),
                'last_updated' => optional($location->updated_at)->toIso8601String(),
                'status' => $this->getAqiStatus($location->aqi)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $validLocations,
            'last_updated' => now()->toIso8601String(),
            'count' => $validLocations->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve AQI locations',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Validate and format coordinate value
 */
private function validateCoordinate($value)
{
    if (!is_numeric($value)) {
        return 0;
    }
    
    return round((float)$value, 6);
}

/**
 * Validate AQI value
 */
private function validateAqi($value)
{
    if (!is_numeric($value)) {
        return null;
    }
    
    $aqi = (int)$value;
    return max(0, min(500, $aqi)); // Ensure AQI is between 0-500
}

/**
 * Get AQI status category
 */
private function getAqiStatus($aqi)
{
    if (!is_numeric($aqi)) return 'unknown';
    
    $aqi = (int)$aqi;
    
    if ($aqi <= 50) return 'good';
    if ($aqi <= 100) return 'moderate';
    if ($aqi <= 150) return 'unhealthy_sensitive';
    if ($aqi <= 200) return 'unhealthy';
    if ($aqi <= 300) return 'very_unhealthy';
    return 'hazardous';
}
 }