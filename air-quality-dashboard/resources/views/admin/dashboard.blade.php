@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Air Quality Monitoring System - Admin Panel</h1>
        <div>
            <a href="{{ route('admin.sensors') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Add Sensor
            </a>
            <a href="{{ route('admin.locations') }}" class="btn btn-success me-2">
                <i class="fas fa-map-marker-alt"></i> Manage Locations
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Sensors</h5>
                    <p class="card-text display-4">{{ $sensors }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Sensors</h5>
                    <p class="card-text display-4">{{ $activeSensors }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Last Updated</h5>
                    <p class="card-text">{{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sensor Map Overview</h5>
        </div>
        <div class="card-body p-0">
            <div id="adminMap" style="height: 500px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Add sensor markers with status colors
    @foreach(App\Models\Sensor::all() as $sensor)
    const marker{{ $sensor->id }} = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], {
        icon: L.divIcon({
            className: 'sensor-marker-{{ $sensor->is_active ? "active" : "inactive" }}',
            html: '<div class="sensor-dot">{{ $sensor->is_active ? "A" : "I" }}</div>',
            iconSize: [30, 30]
        })
    }).addTo(map).bindPopup(`
        <b>{{ $sensor->name }}</b><br>
        Status: {{ $sensor->is_active ? 'Active' : 'Inactive' }}<br>
        Location: {{ $sensor->latitude }}, {{ $sensor->longitude }}
    `);
    @endforeach
});
</script>

<style>
.sensor-marker-active .sensor-dot {
    background: #28a745;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-weight: bold;
}
.sensor-marker-inactive .sensor-dot {
    background: #dc3545;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-weight: bold;
}
#adminMap {
    border-radius: 0 0 8px 8px;
}
</style>
@endpush
@endsection