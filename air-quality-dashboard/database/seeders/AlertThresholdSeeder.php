<?php

namespace Database\Seeders;

use App\Models\AlertThreshold;
use Illuminate\Database\Seeder;

class AlertThresholdSeeder extends Seeder
{
    public function run()
    {
        $thresholds = [
            [
                'level_name' => 'Good',
                'min_value' => 0,
                'max_value' => 50,
                'color_code' => '#00e400',
                'health_implications' => 'Air quality is considered satisfactory, and air pollution poses little or no risk.',
            ],
            [
                'level_name' => 'Moderate',
                'min_value' => 51,
                'max_value' => 100,
                'color_code' => '#ffff00',
                'health_implications' => 'Air quality is acceptable; however, for some pollutants there may be a moderate health concern for a very small number of people.',
            ],
            [
                'level_name' => 'Unhealthy for Sensitive Groups',
                'min_value' => 101,
                'max_value' => 150,
                'color_code' => '#ff7e00',
                'health_implications' => 'Members of sensitive groups may experience health effects. The general public is not likely to be affected.',
            ],
            [
                'level_name' => 'Unhealthy',
                'min_value' => 151,
                'max_value' => 200,
                'color_code' => '#ff0000',
                'health_implications' => 'Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.',
            ],
            [
                'level_name' => 'Very Unhealthy',
                'min_value' => 201,
                'max_value' => 300,
                'color_code' => '#8f3f97',
                'health_implications' => 'Health warnings of emergency conditions. The entire population is more likely to be affected.',
            ],
            [
                'level_name' => 'Hazardous',
                'min_value' => 301,
                'max_value' => 500,
                'color_code' => '#7e0023',
                'health_implications' => 'Health alert: everyone may experience more serious health effects.',
            ],
        ];
        
        foreach ($thresholds as $threshold) {
            AlertThreshold::create($threshold);
        }
    }
}