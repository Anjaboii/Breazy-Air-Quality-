@extends('admin.dashboard')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sensor Locations</h5>
    <div>
        <button class="btn btn-primary me-2" id="addLocationBtn">
            <i class="fas fa-plus"></i> Add Location
        </button>
        <button class="btn btn-secondary" id="cancelAddBtn" style="display: none;">
            <i class="fas fa-times"></i> Cancel
        </button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-0" style="height: 500px;">
        <div id="adminMap" style="height: 100%;"></div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Coordinates</th>
                <th>Status</th>
                <th>Last Reading</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sensors as $sensor)
            <tr>
                <td>{{ $sensor->name }}</td>
                <td>{{ number_format($sensor->latitude, 6) }}, {{ number_format($sensor->longitude, 6) }}</td>
                <td>
                    <span class="badge bg-{{ $sensor->is_active ? 'success' : 'danger' }}">
                        {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    @if($sensor->readings->count() > 0)
                        {{ $sensor->readings->first()->aqi }} AQI
                    @else
                        No readings
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-primary edit-location" 
                            data-id="{{ $sensor->id }}"
                            data-name="{{ $sensor->name }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addLocationForm">
                    <div class="mb-3">
                        <label class="form-label">Sensor Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="modalLatitude" name="latitude" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="modalLongitude" name="longitude" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveLocation">Save Location</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    let markers = [];
    let newMarker = null;
    let clickedLatLng = null;
    
    // Add existing sensors
    @foreach($sensors as $sensor)
    const marker{{ $sensor->id }} = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], {
        draggable: true
    }).addTo(map)
    .bindPopup(`<b>{{ $sensor->name }}</b><br>AQI: {{ $sensor->readings->count() > 0 ? $sensor->readings->first()->aqi : 'N/A' }}`);
    
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
            },
            success: function() {
                location.reload();
            }
        });
    });
    @endforeach
    
    // Add new location
    $('#addLocationBtn').click(function() {
        $(this).hide();
        $('#cancelAddBtn').show();
        
        map.on('click', function(e) {
            clickedLatLng = e.latlng;
            
            if (newMarker) map.removeLayer(newMarker);
            
            newMarker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map)
            .bindPopup('<b>New Sensor Location</b>');
            
            newMarker.on('dragend', function() {
                clickedLatLng = this.getLatLng();
            });
            
            $('#addLocationModal').modal('show');
            $('#modalLatitude').val(e.latlng.lat);
            $('#modalLongitude').val(e.latlng.lng);
        });
    });
    
    $('#cancelAddBtn').click(function() {
        if (newMarker) map.removeLayer(newMarker);
        $('#addLocationBtn').show();
        $(this).hide();
        map.off('click');
        clickedLatLng = null;
    });
    
    // Save new location
    $('#saveLocation').click(function() {
        const formData = {
            name: $('#addLocationForm input[name="name"]').val(),
            latitude: clickedLatLng.lat,
            longitude: clickedLatLng.lng
        };
        
        $.ajax({
            url: '/admin/sensors',
            method: 'POST',
            data: formData,
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endpush