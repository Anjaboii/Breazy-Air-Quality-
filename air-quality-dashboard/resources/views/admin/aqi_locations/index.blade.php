@extends('layouts.app')

@section('content')
<div class="container">
    <h1>AQI Locations</h1>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">All AQI Locations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>AQI</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aqiLocations as $location) <!-- Correct reference to $aqiLocations -->
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                            <td>{{ $location->aqi ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
