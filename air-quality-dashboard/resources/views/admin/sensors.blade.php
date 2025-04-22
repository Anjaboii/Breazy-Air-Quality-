@extends('admin.dashboard')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sensor Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSensorModal">
        <i class="fas fa-plus"></i> Add Sensor
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Status</th>
                <th>Last Reading</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sensors as $sensor)
            <tr>
                <td>{{ $sensor->id }}</td>
                <td>{{ $sensor->name }}</td>
                <td>{{ number_format($sensor->latitude, 4) }}, {{ number_format($sensor->longitude, 4) }}</td>
                <td>
                    <span class="badge bg-{{ $sensor->is_active ? 'success' : 'danger' }}">
                        {{ $sensor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    @if($sensor->readings->count() > 0)
                        {{ $sensor->readings->first()->aqi }} AQI
                        ({{ $sensor->readings->first()->timestamp->diffForHumans() }})
                    @else
                        No readings
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-primary edit-sensor" 
                            data-id="{{ $sensor->id }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editSensorModal"
                            data-name="{{ $sensor->name }}"
                            data-latitude="{{ $sensor->latitude }}"
                            data-longitude="{{ $sensor->longitude }}"
                            data-is_active="{{ $sensor->is_active }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-sensor" data-id="{{ $sensor->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Sensor Modal -->
<div class="modal fade" id="addSensorModal" tabindex="-1">
    <!-- ... existing modal code ... -->
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
                        <input type="number" step="any" class="form-control" name="latitude" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" name="longitude" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active">
                        <label class="form-check-label">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateSensor">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle edit button click
    $('.edit-sensor').click(function() {
        const sensorId = $(this).data('id');
        $('#editSensorForm').attr('action', `/admin/sensors/${sensorId}`);
        $('#editSensorForm input[name="name"]').val($(this).data('name'));
        $('#editSensorForm input[name="latitude"]').val($(this).data('latitude'));
        $('#editSensorForm input[name="longitude"]').val($(this).data('longitude'));
        $('#editSensorForm input[name="is_active"]').prop('checked', $(this).data('is_active') === '1');
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
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });

    // ... existing save and delete handlers ...
});
</script>
@endpush