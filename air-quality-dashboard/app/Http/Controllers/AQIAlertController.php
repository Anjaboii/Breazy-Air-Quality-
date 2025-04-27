<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AQIAlert;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class AQIAlertController extends Controller
{
    /**
     * Get all active AQI alerts for the current user's locations
     */
    public function index()
    {
        // Get user's saved locations or all locations if not authenticated
        if (Auth::check()) {
            $userLocations = Auth::user()->locations()->pluck('id')->toArray();
        } else {
            // For guest users, use session stored locations
            $userLocations = session('saved_locations', []);
        }
        
        // If no locations are saved, return empty array
        if (empty($userLocations)) {
            return response()->json([]);
        }
        
        // Get alerts for user's locations
        $alerts = AQIAlert::whereIn('location_id', $userLocations)
            ->where('is_active', true)
            ->with('location')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($alert) {
                return [
                    'id' => $alert->id,
                    'location' => $alert->location->name,
                    'aqi' => $alert->aqi_value,
                    'message' => $alert->message,
                    'timestamp' => $alert->created_at
                ];
            });
            
        return response()->json($alerts);
    }
    
    /**
     * Dismiss a specific alert
     */
    public function dismiss($id)
    {
        $alert = AQIAlert::findOrFail($id);
        
        // Check if user is authorized to dismiss this alert
        if (Auth::check()) {
            $userLocations = Auth::user()->locations()->pluck('id')->toArray();
        } else {
            $userLocations = session('saved_locations', []);
        }
        
        if (!in_array($alert->location_id, $userLocations)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $alert->is_active = false;
        $alert->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Dismiss all alerts for user's locations
     */
    public function dismissAll()
    {
        if (Auth::check()) {
            $userLocations = Auth::user()->locations()->pluck('id')->toArray();
        } else {
            $userLocations = session('saved_locations', []);
        }
        
        if (empty($userLocations)) {
            return response()->json(['success' => true]);
        }
        
        AQIAlert::whereIn('location_id', $userLocations)
            ->where('is_active', true)
            ->update(['is_active' => false]);
            
        return response()->json(['success' => true]);
    }
}