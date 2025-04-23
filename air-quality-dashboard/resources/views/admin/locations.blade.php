@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>AQI Locations Management</h1>
        <div>
            <button id="addLocationBtn" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Add Location
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Locations</h5>
                    <p class="card-text display-4">{{ $locationCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Good AQI Locations</h5>
                    <p class="card-text display-4">{{ $goodAqiCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Last Updated</h5>
                    <p class="card-text">{{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Map -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">AQI Locations Map</h5>
        </div>
        <div class="card-body p-0">
            <div id="adminMap" style="height: 500px;"></div>
        </div>
    </div>

    <!-- Location List Table -->
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
                            <th>AQI</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aqiLocations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                            <td>{{ $location->aqi ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $location->aqi <= 50 ? 'success' : ($location->aqi <= 100 ? 'warning' : 'danger') }}">
                                    {{ $location->aqi <= 50 ? 'Good' : ($location->aqi <= 100 ? 'Moderate' : 'Unhealthy') }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-location" 
                                        data-id="{{ $location->id }}"
                                        data-name="{{ $location->name }}"
                                        data-latitude="{{ $location->latitude }}"
                                        data-longitude="{{ $location->longitude }}"
                                        data-aqi="{{ $location->aqi }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-location" 
                                        data-id="{{ $location->id }}">
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
<div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New AQI Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addLocationForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Location Name</label>
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
                    <div class="mb-3">
                        <label class="form-label">AQI Value</label>
                        <input type="number" class="form-control" id="modalAqi" name="aqi" required>
                        <small class="text-muted">Will be automatically fetched from API</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveLocation" class="btn btn-primary">
                    <span class="save-text">Save Location</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
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
                    <input type="hidden" name="id" id="editLocationId">
                    <div class="mb-3">
                        <label class="form-label">Location Name</label>
                        <input type="text" class="form-control" name="name" id="editLocationName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.000001" class="form-control" name="latitude" id="editLocationLat" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.000001" class="form-control" name="longitude" id="editLocationLng" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">AQI Value</label>
                        <input type="number" class="form-control" name="aqi" id="editLocationAqi" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateLocation" class="btn btn-primary">
                    <span class="save-text">Update Location</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #adminMap {
        border-radius: 0 0 8px 8px;
    }
    .aqi-marker {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        text-align: center;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid;
    }
    .aqi-good {
        background-color: #28a745;
        border-color: #218838;
        color: white;
    }
    .aqi-moderate {
        background-color: #ffc107;
        border-color: #e0a800;
        color: black;
    }
    .aqi-unhealthy {
        background-color: #dc3545;
        border-color: #c82333;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Store markers
    const locationMarkers = {};
    const markersLayer = L.layerGroup().addTo(map);

    // Function to determine AQI class
    function getAqiClass(aqi) {
        if (!aqi) return 'aqi-moderate';
        if (aqi <= 50) return 'aqi-good';
        if (aqi <= 100) return 'aqi-moderate';
        return 'aqi-unhealthy';
    }

    // Function to create marker
    function createLocationMarker(location) {
        const aqi = location.aqi || 0;
        return L.marker([location.latitude, location.longitude], {
            icon: L.divIcon({
                className: `aqi-marker ${getAqiClass(aqi)}`,
                html: `<div>${aqi}</div>`,
                iconSize: [30, 30]
            })
        }).bindPopup(`
            <b>${location.name}</b><br>
            AQI: ${aqi}<br>
            Location: ${location.latitude}, ${location.longitude}<br>
            <button class="btn btn-sm btn-primary edit-marker" data-id="${location.id}">Edit</button>
            <button class="btn btn-sm btn-danger delete-marker" data-id="${location.id}">Delete</button>
        `);
    }

    // Load existing locations
    @foreach($aqiLocations as $location)
    locationMarkers[{{ $location->id }}] = createLocationMarker(@json($location)).addTo(map);
    @endforeach

    // Modal handling
    const addLocationModal = new bootstrap.Modal('#addLocationModal');
    const editLocationModal = new bootstrap.Modal('#editLocationModal');
    let newMarker = null;
    let clickedLatLng = null;

    // Add new location button click
    document.getElementById('addLocationBtn').addEventListener('click', function() {
        document.getElementById('addLocationForm').reset();
        this.style.display = 'none';

        map.on('click', function(e) {
            clickedLatLng = e.latlng;

            // Remove existing marker if any
            if (newMarker) map.removeLayer(newMarker);

            // Create new marker
            newMarker = L.marker(e.latlng, { draggable: true }).addTo(map)
                .bindPopup('<b>New AQI Location</b>');

            newMarker.on('dragend', function() {
                clickedLatLng = this.getLatLng();
                updateLocationForm(clickedLatLng.lat, clickedLatLng.lng);
            });

            // Update form and fetch AQI
            updateLocationForm(e.latlng.lat, e.latlng.lng);
            
            // Show modal
            addLocationModal.show();
        });
    });

    // Update location form with coordinates and fetch AQI
    function updateLocationForm(lat, lng) {
        document.getElementById('modalLatitude').value = lat;
        document.getElementById('modalLongitude').value = lng;
        
        // Fetch AQI from API
        fetchAqi(lat, lng);
    }

    // Fetch AQI from WAQI API
    function fetchAqi(lat, lng) {
        const aqiInput = document.getElementById('modalAqi');
        aqiInput.value = 'Loading...';
        
        fetch(`https://api.waqi.info/feed/geo:${lat};${lng}/?token={{ env('WAQI_API_KEY') }}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.data && data.data.aqi) {
                    aqiInput.value = data.data.aqi;
                } else {
                    aqiInput.value = 0;
                    alert('Unable to fetch AQI data. Please enter manually.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                aqiInput.value = 0;
                alert('Error fetching AQI data. Please enter manually.');
            });
    }

    // Save new location
    document.getElementById('saveLocation').addEventListener('click', async function() {
        const form = document.getElementById('addLocationForm');
        const formData = new FormData(form);
        const submitBtn = this;
        const saveText = submitBtn.querySelector('.save-text');
        const spinner = submitBtn.querySelector('.spinner-border');

        try {
            // Show loading state
            submitBtn.disabled = true;
            saveText.textContent = 'Saving...';
            spinner.classList.remove('d-none');

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
            const newMarker = createLocationMarker(data.location).addTo(map);
            locationMarkers[data.location.id] = newMarker;

            // Show success and reset form
            alert('Location added successfully!');
            addLocationModal.hide();

            // Refresh the page to update the table and counters
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            saveText.textContent = 'Save Location';
            spinner.classList.add('d-none');
        }
    });

    // Edit location
    document.body.addEventListener('click', function(event) {
        if (event.target && event.target.closest('.edit-location')) {
            const locationId = event.target.closest('.edit-location').getAttribute('data-id');
            editLocation(locationId);
        }

        if (event.target && event.target.closest('.delete-location')) {
            const locationId = event.target.closest('.delete-location').getAttribute('data-id');
            deleteLocation(locationId);
        }
    });

    // Edit location function
    async function editLocation(locationId) {
        try {
            const response = await fetch(`/admin/aqi_locations/${locationId}/edit`);
            const location = await response.json();

            // Populate edit form
            document.getElementById('editLocationId').value = location.id;
            document.getElementById('editLocationName').value = location.name;
            document.getElementById('editLocationLat').value = location.latitude;
            document.getElementById('editLocationLng').value = location.longitude;
            document.getElementById('editLocationAqi').value = location.aqi;

            // Show edit modal
            editLocationModal.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Error loading location data');
        }
    }

    // Update location
    document.getElementById('updateLocation').addEventListener('click', async function() {
        const form = document.getElementById('editLocationForm');
        const formData = new FormData(form);
        const submitBtn = this;
        const saveText = submitBtn.querySelector('.save-text');
        const spinner = submitBtn.querySelector('.spinner-border');

        try {
            // Show loading state
            submitBtn.disabled = true;
            saveText.textContent = 'Updating...';
            spinner.classList.remove('d-none');

            const response = await fetch(`/admin/aqi_locations/${formData.get('id')}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to update location');
            }

            // Update marker on the map
            if (locationMarkers[data.location.id]) {
                map.removeLayer(locationMarkers[data.location.id]);
                locationMarkers[data.location.id] = createLocationMarker(data.location).addTo(map);
            }

            // Show success and close modal
            alert('Location updated successfully!');
            editLocationModal.hide();

            // Refresh the page to update the table
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            saveText.textContent = 'Update Location';
            spinner.classList.add('d-none');
        }
    });

    // Delete location function
    async function deleteLocation(locationId) {
        if (!confirm('Are you sure you want to delete this location?')) return;

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
                map.removeLayer(locationMarkers[locationId]);
                delete locationMarkers[locationId];
            }

            // Refresh the page to update the table
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        }
    }

    // Cleanup when modal is closed
    document.getElementById('addLocationModal').addEventListener('hidden.bs.modal', function() {
        if (newMarker) map.removeLayer(newMarker);
        document.getElementById('addLocationBtn').style.display = 'block';
        map.off('click');
    });
});
</script>
@endpush