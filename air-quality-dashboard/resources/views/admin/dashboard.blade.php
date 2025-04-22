@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <p>Total Sensors: {{ $sensors }}</p>
                    <p>Active Sensors: {{ $activeSensors }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.sensors') }}">Sensors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.locations') }}">Locations</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @yield('admin-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection