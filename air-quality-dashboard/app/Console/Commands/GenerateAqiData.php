<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use App\Models\AqiReading;
use App\Models\AlertThreshold;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateAqiData extends Command
{
    protected $signature = 'aqi:generate';
    protected $description = 'Generate simulated AQI readings for all active sensors';

    public function handle()
    {
        $sensors = Sensor::where('is_active', true)->get();
        
        if ($sensors->isEmpty()) {
            $this->info('No active sensors found. Please add sensors first.');
            return;
        }
        
        // Get all thresholds for category determination
        $thresholds = AlertThreshold::orderBy('min_value')->get();
        
        foreach ($sensors as $sensor) {
            // Base AQI - can be adjusted to create more realistic patterns
            $baseAqi = mt_rand(30, 300);
            
            // Add some random variation
            $variation = mt_rand(-10, 10);
            $aqiValue = max(0, $baseAqi + $variation);
            
            // Determine category based on AQI value
            $category = $this->determineCategory($aqiValue, $thresholds);
            
            // Generate component pollutant values based on AQI
            $pm25 = $aqiValue * 0.6 + mt_rand(-5, 5);
            $pm10 = $aqiValue * 0.8 + mt_rand(-10, 10);
            $o3 = $aqiValue * 0.4 + mt_rand(-8, 8);
            $no2 = $aqiValue * 0.3 + mt_rand(-5, 5);
            $so2 = $aqiValue * 0.2 + mt_rand(-3, 3);
            $co = $aqiValue * 0.1 + mt_rand(-2, 2);
            
            // Create new reading
            AqiReading::create([
                'sensor_id' => $sensor->id,
                'aqi_value' => $aqiValue,
                'pm25' => $pm25,
                'pm10' => $pm10,
                'o3' => $o3,
                'no2' => $no2,
                'so2' => $so2,
                'co' => $co,
                'category' => $category,
                'reading_timestamp' => Carbon::now(),
            ]);
        }
        
        $this->info('Generated AQI readings for ' . $sensors->count() . ' sensors.');
    }
    
    private function determineCategory($aqi, $thresholds)
    {
        foreach ($thresholds as $threshold) {
            if ($aqi >= $threshold->min_value && $aqi <= $threshold->max_value) {
                return $threshold->level_name;
            }
        }
        
        return 'Hazardous'; // Default for extremely high values
    }
}