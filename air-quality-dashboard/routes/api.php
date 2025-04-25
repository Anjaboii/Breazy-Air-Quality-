<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AqiLocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



// Removed the duplicate route for aqi-locations
Route::get('/sensors', [DashboardController::class, 'getSensors']);
Route::get('/sensors/{sensor}/readings', [DashboardController::class, 'getSensorReadings']);
Route::get('/aqi-locations', [AqiLocationController::class, 'index']);
// Remove this line since it creates a duplicate path with 'api/api/aqi-locations'
// Route::get('/api/aqi-locations', [AQILocationController::class, 'getLocations']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// AQI Data Endpoint
Route::get('/aqi-data', function () {
    // Your WAQI API token
    $token = config('services.waqi.token', '4b98b49468bc4a44cc2df7ac4e0007163f430796');
    
    // Configured locations to monitor
    $locations = config('aqi.locations', [
        ['id' => 1387, 'name' => "US Diplomatic Post, Colombo"],
        ['id' => 1388, 'name' => "Colombo Fort"],
        ['id' => 1389, 'name' => "Kandy"]
    ]);
    
    $results = [];
    
    foreach ($locations as $location) {
        try {
            $response = Http::get("https://api.waqi.info/feed/@{$location['id']}/?token={$token}");
            $data = $response->json();
            
            if ($data['status'] !== "ok") continue;
            
            $currentAqi = $data['data']['aqi'] ?? null;
            $pm25 = $data['data']['iaqi']['pm25']['v'] ?? null;
            $time = date('H:i', strtotime($data['data']['time']['iso']));
            
            // If no direct AQI but we have PM2.5, calculate AQI
            if (!$currentAqi && isset($data['data']['iaqi']['pm25']['v'])) {
                $currentAqi = pm25ToAqi($data['data']['iaqi']['pm25']['v']);
            }
            
            $results[] = [
                'id' => $location['id'],
                'name' => $location['name'],
                'currentAqi' => $currentAqi,
                'pm25' => $pm25,
                'time' => $time
            ];
        } catch (\Exception $e) {
            continue;
        }
    }
    
    return response()->json(['locations' => $results]);
});

// Helper function to convert PM2.5 to AQI
function pm25ToAqi($pm25) {
    if ($pm25 <= 12) return round((50/12) * $pm25);
    if ($pm25 <= 35.4) return round(((100-51)/(35.4-12)) * ($pm25-12) + 51);
    if ($pm25 <= 55.4) return round(((150-101)/(55.4-35.4)) * ($pm25-35.4) + 101);
    if ($pm25 <= 150.4) return round(((200-151)/(150.4-55.4)) * ($pm25-55.4) + 151);
    if ($pm25 <= 250.4) return round(((300-201)/(250.4-150.4)) * ($pm25-150.4) + 201);
    return round(((500-301)/(500.4-250.4)) * ($pm25-250.4) + 301);
}