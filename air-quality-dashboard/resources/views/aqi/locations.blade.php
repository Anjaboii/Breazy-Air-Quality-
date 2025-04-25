@extends('layouts.app')

@section('content')
    <h1>AQI Locations</h1>
    <ul>
        @foreach ($locations as $location)
            <li>
                <a href="{{ route('aqi.location.show', $location->id) }}">{{ $location->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
