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

    <!-- Add/Edit Sensor Modal -->
    <div class="modal fade" id="sensorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Sensor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sensorForm">
                        @csrf
                        <input type="hidden" id="sensorId" name="sensor_id">
                        <div class="mb-3">
                            <label class="form-label">Sensor Name</label>
                            <input type="text" class="form-control" name="name" id="sensorName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.000001" class="form-control" name="latitude" id="sensorLat" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.000001" class="form-control" name="longitude" id="sensorLng" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="sensorActive" value="1" checked>
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
                            <th>Sensor Location</th>
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
    .sensor-dot {
        position: relative;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 12px;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s ease;
    }

    .sensor-dot.active {
        background-color: #28a745;
    }

    .sensor-dot.inactive {
        background-color: #dc3545;
    }

    .sensor-dot:hover {
        transform: scale(1.2);
        z-index: 1000;
    }

    .sensor-pulse {
        position: absolute;
        top: -4px;
        left: -4px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .sensor-pulse.active {
        background-color: rgba(40, 167, 69, 0.4);
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }
        70% {
            transform: scale(1.2);
            opacity: 0.3;
        }
        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }

    /* Fix for Leaflet popup buttons */
    .leaflet-popup-content button {
        margin-top: 8px;
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map with a default view of Sri Lanka
    const adminMap = L.map('adminMap').setView([6.9271, 79.8612], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(adminMap);

    // Store sensors data, markers and layer group
    const sensorsData = {};
    const sensorMarkers = {};
    const sensorLayer = L.layerGroup().addTo(adminMap);

    // Function to create marker HTML
    function createMarkerHtml(sensor) {
        const activeClass = sensor.is_active ? 'active' : 'inactive';
        const statusText = sensor.is_active ? 'A' : 'I';
        
        return `
            <div class="sensor-container">
                <div class="sensor-pulse ${activeClass}"></div>
                <div class="sensor-dot ${activeClass}">${statusText}</div>
            </div>
        `;
    }

    // Function to create marker
    function createMarker(sensor) {
        // Store sensor data for later use
        sensorsData[sensor.id] = sensor;
        
        return L.marker([sensor.latitude, sensor.longitude], {
            icon: L.divIcon({
                className: 'sensor-icon-container',
                html: createMarkerHtml(sensor),
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            })
        }).bindPopup(() => {
            // Create popup content with up-to-date sensor data
            const currentSensor = sensorsData[sensor.id];
            return `
                <div class="sensor-popup">
                    <h5>${currentSensor.name}</h5>
                    <p>Status: <span class="badge bg-${currentSensor.is_active ? 'success' : 'danger'}">
                        ${currentSensor.is_active ? 'Active' : 'Inactive'}
                    </span></p>
                    <p>Location: ${currentSensor.latitude}, ${currentSensor.longitude}</p>
                    <div class="popup-actions">
                        <button class="btn btn-sm btn-warning edit-marker" data-id="${currentSensor.id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-info toggle-marker" data-id="${currentSensor.id}">
                            <i class="fas fa-power-off"></i> Toggle
                        </button>
                        <button class="btn btn-sm btn-danger delete-marker" data-id="${currentSensor.id}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `;
        });
    }

    // Load existing sensors
    @foreach($sensors as $sensor)
    const sensor{{ $sensor->id }} = @json($sensor);
    sensorMarkers[{{ $sensor->id }}] = createMarker(sensor{{ $sensor->id }}).addTo(adminMap);
    @endforeach

    // Fit map to show all markers if there are any
    if (Object.keys(sensorMarkers).length > 0) {
        const group = new L.featureGroup(Object.values(sensorMarkers));
        adminMap.fitBounds(group.getBounds().pad(0.2));
    }

    // Modal instance
    const sensorModal = new bootstrap.Modal('#sensorModal');
    let isEditMode = false;

    // Click listener for Add Sensor button
    document.getElementById('openSensorForm').addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Add New Sensor';
        document.getElementById('sensorId').value = '';
        document.getElementById('sensorForm').reset();
        isEditMode = false;
        sensorModal.show();
    });

    // Save sensor functionality
    document.getElementById('saveSensor').addEventListener('click', async function() {
        const form = document.getElementById('sensorForm');
        const formData = new FormData(form);
        const submitBtn = this;
        const sensorId = document.getElementById('sensorId').value;

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            let url, method;
            
            if (isEditMode && sensorId) {
                // Update existing sensor
                url = `/admin/sensors/${sensorId}`;
                method = 'PUT';
                formData.append('_method', 'PUT'); // Laravel method spoofing
            } else {
                // Create new sensor
                url = '{{ route("admin.sensors.store") }}';
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
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

            // Update or create marker on map
            const sensor = data.sensor;
            
            if (isEditMode && sensorMarkers[sensor.id]) {
                // Update existing marker
                sensorLayer.removeLayer(sensorMarkers[sensor.id]);
                sensorsData[sensor.id] = sensor;
            }
            
            sensorMarkers[sensor.id] = createMarker(sensor).addTo(sensorLayer);

            // Show success message
            alert(isEditMode ? 'Sensor updated successfully!' : 'Sensor added successfully!');
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

    // Edit sensor functionality
    function editSensor(sensorId) {
        const sensor = sensorsData[sensorId];
        if (!sensor) {
            alert('Sensor data not found');
            return;
        }

        // Set form fields
        document.getElementById('modalTitle').textContent = 'Edit Sensor';
        document.getElementById('sensorId').value = sensor.id;
        document.getElementById('sensorName').value = sensor.name;
        document.getElementById('sensorLat').value = sensor.latitude;
        document.getElementById('sensorLng').value = sensor.longitude;
        document.getElementById('sensorActive').checked = sensor.is_active;
        
        isEditMode = true;
        sensorModal.show();
    }

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

            // Update local data
            const sensor = data.sensor;
            sensorsData[sensorId] = sensor;
            
            // Update marker on the map
            const marker = sensorMarkers[sensorId];
            sensorLayer.removeLayer(marker);
            sensorMarkers[sensorId] = createMarker(sensor).addTo(sensorLayer);

            // Toast notification
            alert(`Sensor ${sensor.is_active ? 'activated' : 'deactivated'} successfully!`);

            // Refresh the page to update the table
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        }
    }

    // Delete sensor functionality
    async function deleteSensor(sensorId) {
        if (!confirm('Are you sure you want to delete this sensor?')) {
            return;
        }
        
        try {
            const response = await fetch(`/admin/sensors/${sensorId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete sensor');
            }

            // Remove from map
            if (sensorMarkers[sensorId]) {
                sensorLayer.removeLayer(sensorMarkers[sensorId]);
                delete sensorMarkers[sensorId];
                delete sensorsData[sensorId];
            }
            
            alert('Sensor deleted successfully!');
            
            // Refresh page to update table and counters
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message || 'Check console for details'}`);
        }
    }

    // Use event delegation for table buttons
    document.querySelector('table').addEventListener('click', function(event) {
        const target = event.target.closest('.edit-sensor, .toggle-sensor, .delete-sensor');
        if (!target) return;
        
        const sensorId = target.getAttribute('data-id');
        
        if (target.classList.contains('edit-sensor')) {
            editSensor(sensorId);
        } else if (target.classList.contains('toggle-sensor')) {
            toggleSensor(sensorId);
        } else if (target.classList.contains('delete-sensor')) {
            deleteSensor(sensorId);
        }
    });

    // Map popup button event handling
    adminMap.on('popupopen', function(e) {
        const popup = e.popup;
        const container = popup.getElement();
        
        if (!container) return;
        
        // Add event listeners to popup buttons
        const editBtn = container.querySelector('.edit-marker');
        const toggleBtn = container.querySelector('.toggle-marker');
        const deleteBtn = container.querySelector('.delete-marker');
        
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                const sensorId = this.getAttribute('data-id');
                editSensor(sensorId);
                popup.close();
            });
        }
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const sensorId = this.getAttribute('data-id');
                toggleSensor(sensorId);
                popup.close();
            });
        }
        
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const sensorId = this.getAttribute('data-id');
                deleteSensor(sensorId);
                popup.close();
            });
        }
    });
});
</script>
@endpush