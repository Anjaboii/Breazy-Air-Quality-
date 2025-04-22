@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Air Quality Monitoring System - Admin Panel</h1>
        <div>
            <button id="openSensorForm" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Add Sensor
            </button>
            <a href="{{ route('admin.locations') }}" class="btn btn-success me-2">
                <i class="fas fa-map-marker-alt"></i> Manage Locations
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Sensors</h5>
                    <p class="card-text display-4">{{ $sensorCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Sensors</h5>
                    <p class="card-text display-4">{{ $activeSensorCount }}</p>
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

    <!-- Add Sensor Modal -->
    <div class="modal fade" id="sensorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Sensor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sensorForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Sensor Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.000001" class="form-control" name="latitude" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.000001" class="form-control" name="longitude" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                            <label class="form-check-label">Active Sensor</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveSensor" class="btn btn-primary">Save Sensor</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sensor Map -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Sensor Map Overview</h5>
        </div>
        <div class="card-body p-0">
            <div id="adminMap" style="height: 500px;"></div>
        </div>
    </div>

    <!-- Sensor List Table -->
    <div class="card">
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
                                <button class="btn btn-sm btn-warning edit-sensor" data-id="{{ $sensor->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-info toggle-sensor" data-id="{{ $sensor->id }}">
                                    <i class="fas fa-power-off"></i> Toggle
                                </button>
                                <button class="btn btn-sm btn-danger delete-sensor" data-id="{{ $sensor->id }}">
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .sensor-marker-active .sensor-dot {
        background: #28a745;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-weight: bold;
    }
    .sensor-marker-inactive .sensor-dot {
        background: #dc3545;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-weight: bold;
    }
    #adminMap {
        border-radius: 0 0 8px 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const adminMap = L.map('adminMap').setView([6.9271, 79.8612], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(adminMap);

    // Store markers and layer group
    const sensorMarkers = {};
    const sensorLayer = L.layerGroup().addTo(adminMap);

    // Function to create marker
    function createMarker(sensor) {
        return L.marker([sensor.latitude, sensor.longitude], {
            icon: L.divIcon({
                className: `sensor-marker-${sensor.is_active ? 'active' : 'inactive'}`,
                html: `<div class="sensor-dot">${sensor.is_active ? 'A' : 'I'}</div>`,
                iconSize: [30, 30]
            })
        }).bindPopup(`  
            <b>${sensor.name}</b><br>
            Status: ${sensor.is_active ? 'Active' : 'Inactive'}<br>
            Location: ${sensor.latitude}, ${sensor.longitude}<br>
            <button class="btn btn-sm btn-warning edit-marker" data-id="${sensor.id}">Edit</button>
            <button class="btn btn-sm btn-info toggle-marker" data-id="${sensor.id}">Toggle</button>
            <button class="btn btn-sm btn-danger delete-marker" data-id="${sensor.id}">Delete</button>
        `);
    }

    // Load existing sensors dynamically from the server
    @foreach($sensors as $sensor)
    sensorMarkers[{{ $sensor->id }}] = createMarker(@json($sensor)).addTo(adminMap);
    @endforeach

    // Modal handling for "Add Sensor" button
    const sensorModal = new bootstrap.Modal('#sensorModal');
    document.getElementById('openSensorForm').addEventListener('click', () => {
        document.getElementById('sensorForm').reset();
        sensorModal.show();
    });

    // Save sensor with enhanced error handling
    document.getElementById('saveSensor').addEventListener('click', async function() {
        const form = document.getElementById('sensorForm');
        const formData = new FormData(form);
        const submitBtn = this;

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const response = await fetch('{{ route("admin.sensors.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to save sensor');
            }

            // Add new marker to map
            const newMarker = createMarker(data.sensor).addTo(sensorLayer);
            sensorMarkers[data.sensor.id] = newMarker;

            // Show success and reset form
            alert('Sensor added successfully!');
            sensorModal.hide();

            // Refresh the page to update the table and counters
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Sensor';
        }
    });

    // Use event delegation for dynamically added "edit", "toggle", and "delete" buttons
    document.body.addEventListener('click', function(event) {
        if (event.target && event.target.matches('.edit-sensor')) {
            const sensorId = event.target.getAttribute('data-id');
            alert('Edit functionality for sensor ID: ' + sensorId);
        }

        if (event.target && event.target.matches('.toggle-sensor')) {
            const sensorId = event.target.getAttribute('data-id');
            toggleSensor(sensorId);
        }

        if (event.target && event.target.matches('.delete-sensor')) {
            const sensorId = event.target.getAttribute('data-id');

            if(confirm('Are you sure you want to delete this sensor?')) {
                fetch(`/admin/sensors/${sensorId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Remove from map
                        if(sensorMarkers[sensorId]) {
                            sensorLayer.removeLayer(sensorMarkers[sensorId]);
                            delete sensorMarkers[sensorId];
                        }
                        // Refresh page to update table and counters
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting sensor');
                });
            }
        }
    });

    // Toggle Sensor Active Status
    async function toggleSensor(sensorId) {
        try {
            const response = await fetch(`/admin/sensors/${sensorId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to toggle sensor');
            }

            // Toggle marker on the map
            const sensor = data.sensor;
            const marker = sensorMarkers[sensorId];
            marker.setIcon(L.divIcon({
                className: `sensor-marker-${sensor.is_active ? 'active' : 'inactive'}`,
                html: `<div class="sensor-dot">${sensor.is_active ? 'A' : 'I'}</div>`,
                iconSize: [30, 30]
            }));

            // Refresh the page to update the table
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        }
    }
});
</script>
@endpush
