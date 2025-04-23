@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>AQI Locations Management</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <button id="openLocationForm" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add AQI Location
            </button>
        </div>
    </div>

    <!-- AQI Location Map -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">AQI Locations Map</h5>
        </div>
        <div class="card-body p-0">
            <div id="aqiMap" style="height: 500px;"></div>
        </div>
    </div>

    <!-- AQI Locations Table -->
    <div class="card">
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
                            <th>Current AQI</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aqiLocations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                            <td>
                                <span class="badge bg-{{ getAqiColorClass($location->aqi_value) }}">
                                    {{ $location->aqi_value }}
                                </span>
                            </td>
                            <td>{{ $location->last_updated }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-location" data-id="{{ $location->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-info refresh-aqi" data-id="{{ $location->id }}">
                                    <i class="fas fa-sync"></i> Refresh AQI
                                </button>
                                <button class="btn btn-sm btn-danger delete-location" data-id="{{ $location->id }}">
                                    <i class="fas fa-trash"></i> Delete
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
<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New AQI Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="locationForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Location Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.000001" class="form-control" name="latitude" id="latitude" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.000001" class="form-control" name="longitude" id="longitude" required>
                    </div>
                    <div class="mb-3">
                        <button type="button" id="fetchAqiBtn" class="btn btn-info">Fetch AQI Data</button>
                    </div>
                    <div id="aqiDataSection" class="mb-3 d-none">
                        <div class="card">
                            <div class="card-header">AQI Data</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">AQI Value</label>
                                    <input type="number" class="form-control" name="aqi_value" id="aqi_value" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveLocation" class="btn btn-primary" disabled>Save Location</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Location Modal -->
<div class="modal fade" id="editLocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit AQI Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editLocationForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="location_id" id="edit_location_id">
                    <div class="mb-3">
                        <label class="form-label">Location Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.000001" class="form-control" name="latitude" id="edit_latitude" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.000001" class="form-control" name="longitude" id="edit_longitude" required>
                    </div>
                    <div class="mb-3">
                        <button type="button" id="editFetchAqiBtn" class="btn btn-info">Refresh AQI Data</button>
                    </div>
                    <div id="editAqiDataSection" class="mb-3">
                        <div class="card">
                            <div class="card-header">AQI Data</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">AQI Value</label>
                                    <input type="number" class="form-control" name="aqi_value" id="edit_aqi_value" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateLocation" class="btn btn-primary">Update Location</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .aqi-marker-good .aqi-dot,
    .aqi-marker-moderate .aqi-dot,
    .aqi-marker-unhealthy-sensitive .aqi-dot,
    .aqi-marker-unhealthy .aqi-dot,
    .aqi-marker-very-unhealthy .aqi-dot,
    .aqi-marker-hazardous .aqi-dot {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-weight: bold;
        color: white;
    }
    
    .aqi-marker-good .aqi-dot { background: #00e400; }
    .aqi-marker-moderate .aqi-dot { background: #ffff00; color: #333; }
    .aqi-marker-unhealthy-sensitive .aqi-dot { background: #ff7e00; }
    .aqi-marker-unhealthy .aqi-dot { background: #ff0000; }
    .aqi-marker-very-unhealthy .aqi-dot { background: #8f3f97; }
    .aqi-marker-hazardous .aqi-dot { background: #7e0023; }
    
    #aqiMap {
        border-radius: 0 0 8px 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const aqiMap = L.map('aqiMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(aqiMap);

    // Store markers and layer group
    const locationMarkers = {};
    const locationLayer = L.layerGroup().addTo(aqiMap);

    // Function to get AQI category and marker class
    function getAqiCategoryAndClass(aqiValue) {
        if (aqiValue <= 50) {
            return { category: 'Good', class: 'aqi-marker-good' };
        } else if (aqiValue <= 100) {
            return { category: 'Moderate', class: 'aqi-marker-moderate' };
        } else if (aqiValue <= 150) {
            return { category: 'Unhealthy for Sensitive Groups', class: 'aqi-marker-unhealthy-sensitive' };
        } else if (aqiValue <= 200) {
            return { category: 'Unhealthy', class: 'aqi-marker-unhealthy' };
        } else if (aqiValue <= 300) {
            return { category: 'Very Unhealthy', class: 'aqi-marker-very-unhealthy' };
        } else {
            return { category: 'Hazardous', class: 'aqi-marker-hazardous' };
        }
    }

    // Function to create marker
    function createMarker(location) {
        const aqiInfo = getAqiCategoryAndClass(location.aqi_value);
        return L.marker([location.latitude, location.longitude], {
            icon: L.divIcon({
                className: aqiInfo.class,
                html: `<div class="aqi-dot">${location.aqi_value}</div>`,
                iconSize: [30, 30]
            })
        }).bindPopup(`  
            <b>${location.name}</b><br>
            AQI: ${location.aqi_value} (${aqiInfo.category})<br>
            Location: ${location.latitude}, ${location.longitude}<br>
            Last Updated: ${location.last_updated}<br>
            <button class="btn btn-sm btn-warning edit-marker" data-id="${location.id}">Edit</button>
            <button class="btn btn-sm btn-info refresh-marker" data-id="${location.id}">Refresh AQI</button>
            <button class="btn btn-sm btn-danger delete-marker" data-id="${location.id}">Delete</button>
        `);
    }

    // Load existing locations
    @foreach($aqiLocations as $location)
    locationMarkers[{{ $location->id }}] = createMarker(@json($location)).addTo(aqiMap);
    @endforeach

    // Open location modal
    const locationModal = new bootstrap.Modal('#locationModal');
    document.getElementById('openLocationForm').addEventListener('click', () => {
        document.getElementById('locationForm').reset();
        document.getElementById('aqiDataSection').classList.add('d-none');
        document.getElementById('saveLocation').disabled = true;
        locationModal.show();
    });

    // Fetch AQI data from WAQI API
    document.getElementById('fetchAqiBtn').addEventListener('click', async function() {
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        
        if (!latitude || !longitude) {
            alert('Please enter latitude and longitude');
            return;
        }
        
        const fetchBtn = this;
        fetchBtn.disabled = true;
        fetchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Fetching...';
        
        try {
            const response = await fetch(`/admin/aqi/fetch?latitude=${latitude}&longitude=${longitude}`);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to fetch AQI data');
            }
            
            // Display AQI data
            document.getElementById('aqi_value').value = data.aqi;
            
            document.getElementById('aqiDataSection').classList.remove('d-none');
            document.getElementById('saveLocation').disabled = false;
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Failed to fetch AQI data'}`);
        } finally {
            fetchBtn.disabled = false;
            fetchBtn.innerHTML = 'Fetch AQI Data';
        }
    });

    // Save new location
    document.getElementById('saveLocation').addEventListener('click', async function() {
        const form = document.getElementById('locationForm');
        const formData = new FormData(form);
        const submitBtn = this;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            const response = await fetch('{{ route("admin.aqi_locations.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to save location');
            }
            
            // Add new marker to map
            const newMarker = createMarker(data.location).addTo(locationLayer);
            locationMarkers[data.location.id] = newMarker;
            
            alert('Location added successfully!');
            locationModal.hide();
            
            // Refresh the page to update the table
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Location';
        }
    });

    // Edit Location
    document.body.addEventListener('click', function(event) {
        if (event.target && event.target.matches('.edit-location, .edit-marker')) {
            const locationId = event.target.getAttribute('data-id');
            
            fetch(`/admin/aqi_locations/${locationId}/edit`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.location) {
                    const location = data.location;
                    document.getElementById('edit_location_id').value = location.id;
                    document.getElementById('edit_name').value = location.name;
                    document.getElementById('edit_latitude').value = location.latitude;
                    document.getElementById('edit_longitude').value = location.longitude;
                    document.getElementById('edit_aqi_value').value = location.aqi_value;
                    
                    const editLocationModal = new bootstrap.Modal('#editLocationModal');
                    editLocationModal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading location data');
            });
        }
        
        if (event.target && event.target.matches('.refresh-aqi, .refresh-marker')) {
            const locationId = event.target.getAttribute('data-id');
            refreshLocationAqi(locationId);
        }
        
        if (event.target && event.target.matches('.delete-location, .delete-marker')) {
            const locationId = event.target.getAttribute('data-id');
            
            if(confirm('Are you sure you want to delete this location?')) {
                deleteLocation(locationId);
            }
        }
    });

    // Refresh AQI in edit modal
    document.getElementById('editFetchAqiBtn').addEventListener('click', async function() {
        const latitude = document.getElementById('edit_latitude').value;
        const longitude = document.getElementById('edit_longitude').value;
        
        if (!latitude || !longitude) {
            alert('Please enter latitude and longitude');
            return;
        }
        
        const fetchBtn = this;
        fetchBtn.disabled = true;
        fetchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Fetching...';
        
        try {
            const response = await fetch(`/admin/aqi/fetch?latitude=${latitude}&longitude=${longitude}`);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to fetch AQI data');
            }
            
            document.getElementById('edit_aqi_value').value = data.aqi;
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Failed to fetch AQI data'}`);
        } finally {
            fetchBtn.disabled = false;
            fetchBtn.innerHTML = 'Refresh AQI Data';
        }
    });

    // Update location
    document.getElementById('updateLocation').addEventListener('click', async function() {
        const form = document.getElementById('editLocationForm');
        const formData = new FormData(form);
        const locationId = document.getElementById('edit_location_id').value;
        const submitBtn = this;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            
            const response = await fetch(`/admin/aqi_locations/${locationId}`, {
                method: 'POST', // Laravel forms use POST with _method=PUT
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to update location');
            }
            
            // Update marker on map
            if (locationMarkers[locationId]) {
                locationLayer.removeLayer(locationMarkers[locationId]);
                locationMarkers[locationId] = createMarker(data.location).addTo(locationLayer);
            }
            
            alert('Location updated successfully!');
            const editLocationModal = bootstrap.Modal.getInstance('#editLocationModal');
            editLocationModal.hide();
            
            // Refresh the page
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Update Location';
        }
    });

    // Refresh location AQI data
    async function refreshLocationAqi(locationId) {
        try {
            const response = await fetch(`/admin/aqi_locations/${locationId}/refresh`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to refresh AQI data');
            }
            
            // Update marker on map
            if (locationMarkers[locationId]) {
                locationLayer.removeLayer(locationMarkers[locationId]);
                locationMarkers[locationId] = createMarker(data.location).addTo(locationLayer);
            }
            
            alert('AQI data refreshed successfully!');
            
            // Refresh the page
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Failed to refresh AQI data'}`);
        }
    }

    // Delete location
    async function deleteLocation(locationId) {
        try {
            const response = await fetch(`/admin/aqi_locations/${locationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete location');
            }
            
            // Remove marker from map
            if (locationMarkers[locationId]) {
                locationLayer.removeLayer(locationMarkers[locationId]);
                delete locationMarkers[locationId];
            }
            
            alert('Location deleted successfully!');
            
            // Refresh the page
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Failed to delete location'}`);
        }
    }
});
</script>
@endpush

<?php
// Helper function to determine AQI color class
function getAqiColorClass($aqi) {
    if ($aqi <= 50) return 'success'; // Good
    if ($aqi <= 100) return 'warning'; // Moderate
    if ($aqi <= 150) return 'warning text-dark'; // Unhealthy for Sensitive Groups
    if ($aqi <= 200) return 'danger'; // Unhealthy
    if ($aqi <= 300) return 'purple'; // Very Unhealthy
    return 'dark'; // Hazardous
}
