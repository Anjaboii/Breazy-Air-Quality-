<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\AqiLocation;
use App\Models\AqiHistory;

class FetchAQIData extends Command
{
    protected $signature = 'fetch:aqi-data';
    protected $description = 'Fetch and store AQI data for each location from a public API';

    public function handle()
    {
        // Fetch all locations
        $locations = AqiLocation::all();

        foreach ($locations as $location) {
            // Fetch AQI data from a public API (example: WAQI API)
            $response = Http::get('https://api.waqi.info/feed/geo:' . $location->latitude . ';' . $location->longitude . '/?token=4b98b49468bc4a44cc2df7ac4e0007163f430796');

            if ($response->successful()) {
                $aqiData = $response->json();
                
                if (isset($aqiData['data']['aqi'])) {
                    // Save AQI data into the aqi_histories table
                    AqiHistory::create([
                        'location_id' => $location->id,
                        'aqi' => $aqiData['data']['aqi'],
                        'date' => now(),
                    ]);
                }
            }
        }

        $this->info('AQI data has been fetched and stored.');
    }
}

