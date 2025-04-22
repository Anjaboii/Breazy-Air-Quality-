@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sensor Locations</h1>
    
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Map View</h5>
                <button class="btn btn-primary" id="addLocationBtn">
                    Add New Location
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="adminMap" style="height: 500px;"></div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">All Sensors</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sensors as $sensor)
                        <tr>
                            <td>{{ $sensor->id }}</td>
                            <td>{{ $sensor->name }}</td>
                            <td>{{ $sensor->latitude }}, {{ $sensor->longitude }}</td>
                            <td>
                                <span class="badge bg-{{ $sensor->is_active ? 'success' : 'danger' }}">
                                    {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-location" 
                                        data-id="{{ $sensor->id }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
                        <input type="number" step="0.000001" class="form-control" id="modalLatitude" name="latitude" readonly required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.000001" class="form-control" id="modalLongitude" name="longitude" readonly required>
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
    .bindPopup(`<b>{{ $sensor->name }}</b><br>Status: {{ $sensor->is_active ? 'Active' : 'Inactive' }}`);
    
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
        
        map.on('click', function(e) {
            clickedLatLng = e.latlng;
            
            if (newMarker) map.removeLayer(newMarker);
            
            newMarker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map)
            .bindPopup('<b>New Sensor Location</b>');
            
            newMarker.on('dragend', function() {
                clickedLatLng = this.getLatLng();
                $('#modalLatitude').val(clickedLatLng.lat);
                $('#modalLongitude').val(clickedLatLng.lng);
            });
            
            $('#modalLatitude').val(e.latlng.lat);
            $('#modalLongitude').val(e.latlng.lng);
            $('#addLocationModal').modal('show');
        });
    });
    
    // Save new location
    $('#saveLocation').click(function() {
        const formData = $('#addLocationForm').serialize();
        
        $.ajax({
            url: '/admin/sensors',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#addLocationModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
    
    // Close modal cleanup
    $('#addLocationModal').on('hidden.bs.modal', function () {
        if (newMarker) map.removeLayer(newMarker);
        $('#addLocationBtn').show();
        map.off('click');
    });
});
</script>
@endpush