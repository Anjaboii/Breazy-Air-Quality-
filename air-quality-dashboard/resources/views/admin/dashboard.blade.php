@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="adminMenu" data-bs-toggle="dropdown">
                Admin Options
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.sensors') }}">Manage Sensors</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.locations') }}">Manage Locations</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Sensors</h5>
                    <p class="card-text display-4">{{ $sensors }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Active Sensors</h5>
                    <p class="card-text display-4">{{ $activeSensors }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Last Updated</h5>
                    <p class="card-text">{{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.sensors') }}">Sensors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.locations') }}">Locations</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <h5 class="card-title">System Overview</h5>
            <p class="card-text">Welcome to the Air Quality Monitoring System admin panel.</p>
            <div id="adminMap" style="height: 400px;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    // Add existing sensors
    @foreach(App\Models\Sensor::all() as $sensor)
    L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}])
        .addTo(map)
        .bindPopup(`<b>{{ $sensor->name }}</b><br>Status: {{ $sensor->is_active ? 'Active' : 'Inactive' }}`);
    @endforeach
});
</script>
@endpush
@endsection