@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Colombo Air Quality Dashboard</h1>
        <div class="card">
            <div class="card-body">
                <div id="map" style="height: 500px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>AQI Legend</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #00e400; color: #000;">0-50 (Good)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #ffff00; color: #000;">51-100 (Moderate)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #ff7e00; color: #000;">101-150 (Unhealthy for Sensitive Groups)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #ff0000; color: #fff;">151-200 (Unhealthy)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #99004c; color: #fff;">201-300 (Very Unhealthy)</div>
                    </div>
                    <div class="col-md-2">
                        <div class="aqi-legend" style="background-color: #7e0023; color: #fff;">300+ (Hazardous)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/map.js') }}"></script>
@endpush