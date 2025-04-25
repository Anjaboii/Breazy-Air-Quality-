@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>AQI History for {{ $location->name }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            &larr; Back
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Location ID:</strong> {{ $location->id }}</p>
                    <p><strong>Coordinates:</strong> {{ $location->latitude }}, {{ $location->longitude }}</p>
                </div>
                <div class="col-md-6">
                    @if($location->aqiHistory->isNotEmpty())
                        <p><strong>Latest AQI:</strong> {{ $location->aqiHistory->first()->aqi }}</p>
                        <p><strong>Last Updated:</strong> {{ $location->aqiHistory->first()->date->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Historical Data</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>AQI Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aqiHistory as $record)
                    <tr>
                        <td>{{ $record->date->format('M j, Y H:i') }}</td>
                        <td>{{ $record->aqi }}</td>
                        <td>
                            @php
                                $status = match(true) {
                                    $record->aqi <= 50 => ['class' => 'success', 'text' => 'Good'],
                                    $record->aqi <= 100 => ['class' => 'info', 'text' => 'Moderate'],
                                    $record->aqi <= 150 => ['class' => 'warning', 'text' => 'Unhealthy for Sensitive'],
                                    $record->aqi <= 200 => ['class' => 'orange', 'text' => 'Unhealthy'],
                                    $record->aqi <= 300 => ['class' => 'danger', 'text' => 'Very Unhealthy'],
                                    default => ['class' => 'dark', 'text' => 'Hazardous']
                                };
                            @endphp
                            <span class="badge bg-{{ $status['class'] }}">
                                {{ $status['text'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $aqiHistory->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-orange {
        background-color: #fd7e14;
    }
</style>
@endpush