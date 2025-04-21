<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AirQualityService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.waqi.info';

    public function __construct()
    {
        $this->apiKey = env('4b98b49468bc4a44cc2df7ac4e0007163f430796');
    }

    public function getCityData($city)
    {
        return Cache::remember('waqi_data_'.$city, 3600, function () use ($city) {
            $response = Http::get("{$this->baseUrl}/feed/{$city}/?token={$this->apiKey}");
            return $response->json();
        });
    }

    public function getNearbyStations($lat, $lng)
    {
        return Cache::remember("waqi_nearby_{$lat}_{$lng}", 3600, function () use ($lat, $lng) {
            $response = Http::get("{$this->baseUrl}/map/bounds/?latlng={$lat},{$lng},{$lat},{$lng}&token={$this->apiKey}");
            return $response->json();
        });
    }
}