<!-- resources/views/admin/aqi_locations/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New AQI Location</h1>

    <!-- Display Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('aqi_locations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Location Name</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.000001" class="form-control" name="latitude" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.000001" class="form-control" name="longitude" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Location</button>
    </form>
</div>
@endsection
