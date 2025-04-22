@extends('admin.dashboard')

@section('admin-content')
<h5>Manage Sensor Locations</h5>

<div id="adminMap" style="height: 500px;"></div>

<div class="mt-3">
    <button class="btn btn-primary" id="addLocationBtn">Add New Location</button>
    <button class="btn btn-secondary" id="cancelAddBtn" style="display: none;">Cancel</button>
</div>

<table class="table mt-3">
    <thead>
        <tr>
            <th>Name</th>
            <th>Coordinates</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sensors as $sensor)
        <tr>
            <td>{{ $sensor->name }}</td>
            <td>{{ $sensor->latitude }}, {{ $sensor->longitude }}</td>
            <td>
                <span class="badge bg-{{ $sensor->is_active ? 'success' : 'danger' }}">
                    {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary edit-location" data-id="{{ $sensor->id }}">Edit</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    let markers = [];
    
    // Add existing sensors
    @foreach($sensors as $sensor)
    const marker{{ $sensor->id }} = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], {
        draggable: true
    }).addTo(map)
    .bindPopup(`<b>{{ $sensor->name }}</b><br>AQI: -`);
    
    marker{{ $sensor->id }}.sensorId = {{ $sensor->id }};
    markers.push(marker{{ $sensor->id }});
    
    marker{{ $sensor->id }}.on('dragend', function() {
        const newLatLng = this.getLatLng();
        $.ajax({
            url: `/admin/sensors/${this.sensorId}`,
            method: 'PUT',
            data: {
                latitude: newLatLng.lat,
                longitude: newLatLng.lng
            }
        });
    });
    @endforeach
    
    // Add new location
    let newMarker = null;
    
    $('#addLocationBtn').click(function() {
        $(this).hide();
        $('#cancelAddBtn').show();
        
        map.on('click', function(e) {
            if (newMarker) map.removeLayer(newMarker);
            
            newMarker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map)
            .bindPopup('<b>New Sensor Location</b>');
            
            $('#addLocationBtn').text('Save Location');
        });
    });
    
    $('#cancelAddBtn').click(function() {
        if (newMarker) map.removeLayer(newMarker);
        $('#addLocationBtn').show().text('Add New Location');
        $(this).hide();
        map.off('click');
    });
});
</script>
@endpush