@extends('layouts.app')

@section('content')
    <h1>AQI Data</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Location</th>
                <th>AQI</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($aqiHistory as $history)
                <tr>
                    <td>{{ $history->location->name }}</td>
                    <td>{{ $history->aqi }}</td>
                    <td>{{ $history->date->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
