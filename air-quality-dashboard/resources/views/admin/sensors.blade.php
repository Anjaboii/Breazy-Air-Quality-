@extends('admin.dashboard')

@section('admin-content')
<h5>Manage Sensors</h5>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sensors as $sensor)
        <tr>
            <td>{{ $sensor->id }}</td>
            <td>{{ $sensor->name }}</td>
            <td>
                <span class="badge bg-{{ $sensor->is_active ? 'success' : 'danger' }}">
                    {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary edit-sensor" data-id="{{ $sensor->id }}">Edit</button>
                <button class="btn btn-sm btn-danger delete-sensor" data-id="{{ $sensor->id }}">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSensorModal">Add New Sensor</button>

<!-- Add Sensor Modal -->
<div class="modal fade" id="addSensorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Sensor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSensorForm">
                    <div class="mb-3">
                        <label class="form-label">Sensor Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" name="latitude" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" name="longitude" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSensor">Save</button>
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
            }
        });
    });
});
</script>
@endpush