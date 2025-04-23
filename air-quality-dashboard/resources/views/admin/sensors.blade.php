@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sensor Management</h1>
    
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Sensors</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSensorModal">
                    Add New Sensor
                </button>
            </div>
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
                                <button class="btn btn-sm btn-primary edit-sensor" 
                                        data-id="{{ $sensor->id }}"
                                        data-name="{{ $sensor->name }}"
                                        data-latitude="{{ $sensor->latitude }}"
                                        data-longitude="{{ $sensor->longitude }}"
                                        data-is_active="{{ $sensor->is_active }}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-sensor" 
                                        data-id="{{ $sensor->id }}">
                                    Delete
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

<!-- Add Sensor Modal -->
<div class="modal fade" id="addSensorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Sensor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addLocationForm" method="POST" action="{{ route('admin.aqi_locations.store') }}">
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSensor">Save Sensor</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sensor Modal -->
<div class="modal fade" id="editSensorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sensor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editSensorForm">
                    @method('PUT')
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
                        <input type="checkbox" class="form-check-input" name="is_active">
                        <label class="form-check-label">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateSensor">Update Sensor</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle add sensor
    $('#saveSensor').click(function() {
        const formData = $('#addSensorForm').serialize();
        
        $.ajax({
            url: '/admin/sensors',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#addSensorModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
    
    // Handle edit button click
    $('.edit-sensor').click(function() {
        const sensorId = $(this).data('id');
        $('#editSensorForm').attr('action', `/admin/sensors/${sensorId}`);
        $('#editSensorForm input[name="name"]').val($(this).data('name'));
        $('#editSensorForm input[name="latitude"]').val($(this).data('latitude'));
        $('#editSensorForm input[name="longitude"]').val($(this).data('longitude'));
        $('#editSensorForm input[name="is_active"]').prop('checked', $(this).data('is_active') === '1');
        $('#editSensorModal').modal('show');
    });
    
    // Handle update sensor
    $('#updateSensor').click(function() {
        const formData = $('#editSensorForm').serialize();
        const sensorId = $('#editSensorForm').attr('action').split('/').pop();
        
        $.ajax({
            url: `/admin/sensors/${sensorId}`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#editSensorModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
    
    // Handle delete sensor
    $('.delete-sensor').click(function() {
        if (!confirm('Are you sure you want to delete this sensor?')) return;
        
        const sensorId = $(this).data('id');
        
        $.ajax({
            url: `/admin/sensors/${sensorId}`,
            method: 'DELETE',
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