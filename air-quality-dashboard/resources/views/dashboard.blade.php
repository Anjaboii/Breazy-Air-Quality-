@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        
        

        <div class="card">
            <div class="card-body">
                <div style="position: relative;">
                    <div id="map" style="width: 100%; height: 650px;"></div>
                    <!-- AQI Legend positioned in the bottom right corner of the map -->
                    <div style="position: absolute; bottom: 20px; right: 20px; z-index: 1000; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.2); max-width: 250px;">
                        <h5 style="margin-top: 0; margin-bottom: 10px; font-size: 16px;">AQI Legend</h5>
                        <div style="display: grid; grid-template-columns: 20px auto; gap: 5px; align-items: center;">
                            <div style="width: 15px; height: 15px; background-color: #00e400;"></div>
                            <span style="font-size: 12px;">0-50 (Good)</span>
                            
                            <div style="width: 15px; height: 15px; background-color: #ffff00;"></div>
                            <span style="font-size: 12px;">51-100 (Moderate)</span>
                            
                            <div style="width: 15px; height: 15px; background-color: #ff7e00;"></div>
                            <span style="font-size: 12px;">101-150 (USG)</span>
                            
                            <div style="width: 15px; height: 15px; background-color: #ff0000;"></div>
                            <span style="font-size: 12px;">151-200 (Unhealthy)</span>
                            
                            <div style="width: 15px; height: 15px; background-color: #99004c;"></div>
                            <span style="font-size: 12px;">201-300 (Very Unhealthy)</span>
                            
                            <div style="width: 15px; height: 15px; background-color: #7e0023;"></div>
                            <span style="font-size: 12px;">300+ (Hazardous)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/map.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endpush